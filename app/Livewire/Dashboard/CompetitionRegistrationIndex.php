<?php

namespace App\Livewire\Dashboard;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Competition;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class CompetitionRegistrationIndex extends Component
{
    public function render(): View
    {
        $team = Auth::user()->captainedTeam;
        $registrations = $team ? $team->registrations()->with('payment')->get()->keyBy('competition_id') : collect();

        $competitions = Competition::query()->orderBy('id')->get()->map(function (Competition $competition) use ($team, $registrations): array {
            /** @var Registration|null $registration */
            $registration = $registrations->get($competition->id);

            return [
                'short_name' => $competition->code->value,
                'name' => $competition->name,
                'description' => $competition->description,
                'fee' => 'IDR '.number_format((float) $competition->registration_fee, 0, ',', '.'),
                'accepting_registration' => $competition->acceptsRegistration(),
                'registration_window' => $this->registrationWindow($competition),
                'registered' => $registration !== null,
                'team_name' => $team?->name,
                'registration' => $this->registrationStatus($registration?->status),
                'payment' => $this->paymentStatus($registration?->payment?->status),
                'cta_label' => $registration ? 'View Registration' : 'Review Registration',
                'cta_route' => route('dashboard.registration.show', strtolower($competition->code->value)),
            ];
        });

        return view('livewire.dashboard.competition-registration-index', compact('competitions', 'team'));
    }

    private function registrationWindow(Competition $competition): string
    {
        if (! $competition->registration_open) {
            return 'Registration closed';
        }

        if ($competition->registration_start_at?->isFuture()) {
            return 'Opens '.$competition->registration_start_at->format('d M Y, H:i').' WIB';
        }

        if ($competition->registration_end_at?->isPast()) {
            return 'Closed '.$competition->registration_end_at->format('d M Y, H:i').' WIB';
        }

        return $competition->registration_end_at
            ? 'Closes '.$competition->registration_end_at->format('d M Y, H:i').' WIB'
            : 'Registration open';
    }

    private function registrationStatus(?RegistrationStatus $status): array
    {
        return match ($status) {
            RegistrationStatus::Pending => ['label' => 'Pending', 'tone' => 'warning'],
            RegistrationStatus::Verified => ['label' => 'Verified', 'tone' => 'success'],
            RegistrationStatus::Rejected => ['label' => 'Rejected', 'tone' => 'error'],
            default => ['label' => 'Not registered', 'tone' => 'neutral'],
        };
    }

    private function paymentStatus(?PaymentStatus $status): array
    {
        return match ($status) {
            PaymentStatus::WaitingVerification => ['label' => 'Waiting verification', 'tone' => 'info'],
            PaymentStatus::Verified => ['label' => 'Verified', 'tone' => 'success'],
            PaymentStatus::Rejected => ['label' => 'Rejected', 'tone' => 'error'],
            default => ['label' => 'Not submitted', 'tone' => 'neutral'],
        };
    }
}
