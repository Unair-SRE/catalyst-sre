<?php

namespace App\Livewire\Dashboard;

use App\Enums\RegistrationStatus;
use App\Models\CompetitionRound;
use App\Models\Registration;
use App\Models\Submission;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class SubmissionIndex extends Component
{
    public function render(): View
    {
        return view('livewire.dashboard.submission-index', [
            'competitions' => $this->competitionEntries(),
        ]);
    }

    /** @return array<int, array<string, mixed>> */
    private function competitionEntries(): array
    {
        $registrations = $this->registrations();

        if ($registrations->isEmpty()) {
            return [];
        }

        $submissions = Submission::query()
            ->whereIn('registration_id', $registrations->pluck('id'))
            ->get()
            ->keyBy(fn (Submission $submission): string => $submission->registration_id.'-'.$submission->competition_round_id);

        return $registrations->map(function (Registration $registration) use ($submissions): array {
            $competition = strtolower($registration->competition->code->value);

            $rounds = $registration->competition->rounds
                ->sortBy('round_order')
                ->values()
                ->map(fn (CompetitionRound $round): array => $this->roundEntry(
                    $registration,
                    $round,
                    $competition,
                    $submissions->get($registration->id.'-'.$round->id),
                ));

            return [
                'competition' => [
                    'short_name' => $registration->competition->code->value,
                    'name' => $registration->competition->code->label(),
                ],
                'registration' => [
                    'label' => match ($registration->status) {
                        RegistrationStatus::Verified => 'Verified',
                        RegistrationStatus::Rejected => 'Rejected',
                        default => 'Pending',
                    },
                    'tone' => match ($registration->status) {
                        RegistrationStatus::Verified => 'success',
                        RegistrationStatus::Rejected => 'error',
                        default => 'info',
                    },
                ],
                'rounds' => $rounds->all(),
            ];
        })->values()->all();
    }

    /** @return Collection<int, Registration> */
    private function registrations(): Collection
    {
        $team = Auth::user()->captainedTeam()->first();

        if ($team === null) {
            return new Collection;
        }

        return Registration::query()
            ->where('team_id', $team->id)
            ->with(['competition.rounds'])
            ->orderBy('competition_id')
            ->get();
    }

    /**
     * @return array{name: string, label: string, tone: string, note: string, href: ?string, cta: ?string}
     */
    private function roundEntry(
        Registration $registration,
        CompetitionRound $round,
        string $competition,
        ?Submission $submission,
    ): array {
        if ($registration->status !== RegistrationStatus::Verified) {
            return $this->entry($round, $competition, 'Locked', 'neutral', 'Registration verification required before submission becomes available.');
        }

        $state = $round->windowState();

        if ($state === 'unscheduled') {
            return $this->entry($round, $competition, 'Not scheduled', 'neutral', 'The committee has not announced the submission window yet.');
        }

        $deadline = $this->dateTimeLabel($round->ends_at);

        if ($state === 'upcoming') {
            return $this->entry($round, $competition, 'Upcoming', 'info', 'Opens '.$this->dateTimeLabel($round->starts_at).'.', 'View stage');
        }

        if ($state === 'closed') {
            $note = $submission === null
                ? 'The submission window closed on '.$deadline.'.'
                : $this->submittedLabel($submission).'. The submission window closed on '.$deadline.'.';

            return $this->entry($round, $competition, 'Closed', 'neutral', $note, 'View submission');
        }

        if ($submission !== null) {
            return $this->entry($round, $competition, 'Submitted', 'success', $this->submittedLabel($submission).'. You can replace it until '.$deadline.'.', 'View submission');
        }

        return $this->entry($round, $competition, 'Open', 'info', 'Submit before '.$deadline.'.', 'Open submission');
    }

    /**
     * @return array{name: string, label: string, tone: string, note: string, href: ?string, cta: ?string}
     */
    private function entry(
        CompetitionRound $round,
        string $competition,
        string $label,
        string $tone,
        string $note,
        ?string $cta = null,
    ): array {
        return [
            'name' => $round->name,
            'label' => $label,
            'tone' => $tone,
            'note' => $note,
            'href' => $cta === null ? null : route('dashboard.submission.show', [
                'competition' => $competition,
                'stage' => $round->round_order,
            ]),
            'cta' => $cta,
        ];
    }

    private function submittedLabel(Submission $submission): string
    {
        if ($submission->submitted_at === null) {
            return 'Version '.$submission->version.' submitted';
        }

        return 'Version '.$submission->version.' submitted '.$this->dateTimeLabel($submission->submitted_at);
    }

    private function dateTimeLabel(\DateTimeInterface $value): string
    {
        return CarbonImmutable::instance($value)
            ->timezone('Asia/Jakarta')
            ->format('d M Y, H:i').' WIB';
    }
}
