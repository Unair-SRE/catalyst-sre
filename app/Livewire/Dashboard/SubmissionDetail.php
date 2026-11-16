<?php

namespace App\Livewire\Dashboard;

use App\Actions\Submissions\ConfirmSubmissionUpload;
use App\Actions\Submissions\RequestSubmissionUpload;
use App\Enums\CompetitionCode;
use App\Enums\RegistrationStatus;
use App\Models\Competition;
use App\Models\CompetitionRound;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\Team;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

class SubmissionDetail extends Component
{
    public string $competition;

    public ?string $stage = null;

    public ?string $feedback = null;

    public function mount(string $competition, ?string $stage = null): void
    {
        abort_if(CompetitionCode::tryFrom(strtoupper($competition)) === null, 404);
        $this->competition = strtolower($competition);
        $this->stage = $stage;
        $this->context();
    }

    /**
     * @return array<string, mixed>
     */
    public function requestUpload(string $extension): array
    {
        $context = $this->context();

        try {
            return [
                'ok' => true,
                ...app(RequestSubmissionUpload::class)->handle(
                    Auth::user(), $context['registration'], $context['round'], $extension,
                ),
            ];
        } catch (ValidationException $exception) {
            return ['ok' => false, 'message' => collect($exception->errors())->flatten()->first()];
        }
    }

    /**
     * @return array{ok: bool, message?: string}
     */
    public function confirmUpload(string $publicId): array
    {
        $context = $this->context();

        try {
            app(ConfirmSubmissionUpload::class)->handle(
                Auth::user(), $context['registration'], $context['round'], $publicId,
            );
        } catch (ValidationException $exception) {
            return ['ok' => false, 'message' => collect($exception->errors())->flatten()->first()];
        }

        $this->feedback = 'Submission received.';

        return ['ok' => true];
    }

    public function render(): View
    {
        $context = $this->context();
        $registration = $context['registration'];
        $round = $context['round'];
        $submission = $context['submission'];
        $state = $registration->status === RegistrationStatus::Verified ? $round->windowState() : 'locked';

        return view('livewire.dashboard.submission-detail', [
            'competitionModel' => $context['competition'],
            'round' => $round,
            'submission' => $submission,
            'rounds' => $context['rounds'],
            'state' => $state,
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
            'stageStatus' => match ($state) {
                'locked' => ['label' => 'Locked', 'tone' => 'neutral'],
                'unscheduled' => ['label' => 'Not scheduled', 'tone' => 'neutral'],
                'upcoming' => ['label' => 'Upcoming', 'tone' => 'info'],
                'closed' => ['label' => 'Closed', 'tone' => 'neutral'],
                default => ['label' => 'Open', 'tone' => 'success'],
            },
            'canUpload' => $state === 'open',
            'fileLabel' => $submission === null ? null : [
                'name' => basename($submission->public_id),
                'format' => strtoupper($submission->format),
                'size' => $this->sizeLabel($submission->bytes),
                'version' => $submission->version,
                'submitted_at' => $submission->submitted_at === null ? null : $this->dateTimeLabel($submission->submitted_at),
            ],
        ]);
    }

    /**
     * @return array{competition: Competition, registration: Registration, round: CompetitionRound, rounds: Collection<int, CompetitionRound>, submission: ?Submission}
     */
    private function context(): array
    {
        $code = CompetitionCode::tryFrom(strtoupper($this->competition));
        abort_if($code === null, 404);

        $competition = Competition::query()->where('code', $code)->firstOrFail();
        $team = $this->team();
        $registration = $team?->registrations()->where('competition_id', $competition->id)->first();
        abort_if($registration === null, 404);

        $rounds = $competition->rounds()->orderBy('round_order')->get();
        abort_if($rounds->isEmpty(), 404);

        $round = $this->stage === null
            ? $rounds->first()
            : $rounds->first(fn (CompetitionRound $item): bool => (string) $item->round_order === $this->stage);

        abort_if($round === null, 404);

        $submission = Submission::query()
            ->where('registration_id', $registration->id)
            ->where('competition_round_id', $round->id)
            ->first();

        return compact('competition', 'registration', 'round', 'rounds', 'submission');
    }

    private function team(): ?Team
    {
        return Auth::user()->captainedTeam()->first();
    }

    private function sizeLabel(int $bytes): string
    {
        return $bytes >= 1024 * 1024
            ? round($bytes / 1024 / 1024, 1).' MB'
            : max(1, (int) round($bytes / 1024)).' KB';
    }

    private function dateTimeLabel(\DateTimeInterface $value): string
    {
        return CarbonImmutable::instance($value)->timezone('Asia/Jakarta')->format('d M Y, H:i').' WIB';
    }
}
