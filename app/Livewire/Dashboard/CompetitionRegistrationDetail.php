<?php

namespace App\Livewire\Dashboard;

use App\Actions\Registrations\RegisterTeam;
use App\Enums\CompetitionCode;
use App\Enums\PaymentStatus;
use App\Models\Competition;
use App\Models\Registration;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

class CompetitionRegistrationDetail extends Component
{
    public string $competition;

    public bool $showConfirm = false;

    public ?string $feedback = null;

    public function mount(string $competition): void
    {
        abort_if(CompetitionCode::tryFrom(strtoupper($competition)) === null, 404);
        $this->competition = strtolower($competition);
    }

    public function openConfirmation(): void
    {
        if (! $this->registration() && $this->canRegister()) {
            $this->showConfirm = true;
        }
    }

    public function cancelConfirmation(): void
    {
        $this->showConfirm = false;
    }

    public function submitRegistration(RegisterTeam $registerTeam): void
    {
        $team = $this->team();
        if (! $team) {
            throw ValidationException::withMessages(['team' => 'Create your team before registering.']);
        }

        $registerTeam->handle(Auth::user(), $team, $this->competitionModel());
        $this->showConfirm = false;
        $this->feedback = 'Registration created. Complete the payment to continue.';
    }

    public function render(): View
    {
        $competitionModel = $this->competitionModel();
        $team = $this->team()?->load(['captain', 'members']);
        $registration = $this->registration()?->load('payment');
        $canRegister = $this->canRegister();
        $blockReason = $this->blockReason();
        $paymentStatus = $this->paymentStatus($registration);
        $paymentUrl = $registration ? route('dashboard.registration.payment', $this->competition) : null;

        return view('livewire.dashboard.competition-registration-detail', compact('competitionModel', 'team', 'registration', 'canRegister', 'blockReason', 'paymentStatus', 'paymentUrl'));
    }

    private function competitionModel(): Competition
    {
        $code = CompetitionCode::tryFrom(strtoupper($this->competition));
        abort_if($code === null, 404);

        return Competition::query()->where('code', $code)->firstOrFail();
    }

    private function team(): ?Team
    {
        return Auth::user()->captainedTeam()->first();
    }

    private function registration(): ?Registration
    {
        return $this->team()?->registrations()->where('competition_id', $this->competitionModel()->id)->first();
    }

    private function canRegister(): bool
    {
        $team = $this->team();
        $competition = $this->competitionModel();
        if (! $team || ! $competition->acceptsRegistration() || ! $team->hasCompleteKtm() || $this->registration()) {
            return false;
        }

        return ! ($competition->code->isMainCompetition() && $team->registrations()
            ->whereHas('competition', fn ($query) => $query->whereIn('code', [CompetitionCode::BusinessCase, CompetitionCode::BusinessPlan]))
            ->exists());
    }

    private function blockReason(): ?string
    {
        $team = $this->team();
        $competition = $this->competitionModel();
        if (! $team) {
            return 'Create your team before registering for a competition.';
        }
        if (! $team->hasCompleteKtm()) {
            return 'Upload the captain and every member KTM before registering.';
        }
        if (! $competition->acceptsRegistration()) {
            return 'Registration for this competition is currently closed.';
        }
        if ($competition->code->isMainCompetition() && ! $this->registration() && $team->registrations()->whereHas('competition', fn ($query) => $query->whereIn('code', [CompetitionCode::BusinessCase, CompetitionCode::BusinessPlan]))->exists()) {
            return 'Your team is already registered for another main competition.';
        }

        return null;
    }

    private function paymentStatus(?Registration $registration): array
    {
        return match ($registration?->payment?->status) {
            PaymentStatus::WaitingVerification => ['label' => 'Waiting verification', 'tone' => 'info'],
            PaymentStatus::Verified => ['label' => 'Verified', 'tone' => 'success'],
            PaymentStatus::Rejected => ['label' => 'Rejected', 'tone' => 'error'],
            default => ['label' => 'Not submitted', 'tone' => 'neutral'],
        };
    }
}
