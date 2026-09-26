<?php

namespace App\Livewire\Dashboard;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\SummitOrder;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;

class DatabaseOverview extends Component
{
    public function render(): View
    {
        $user = Auth::user();
        $team = $user->captainedTeam()->with(['captain', 'members', 'registrations.competition', 'registrations.payment'])->first();
        $registrations = $team?->registrations ?? collect();
        $summitOrder = SummitOrder::query()->where('user_id', $user->id)->latest()->first();

        $actions = collect();
        if (! $team) {
            $actions->push(['title' => 'Create your team', 'description' => 'Set up your team before registering for a competition.', 'label' => 'Manage Team', 'href' => route('dashboard.team.index')]);
        } elseif (! $team->hasDocumentsFolder()) {
            $actions->push(['title' => 'Complete team documents', 'description' => 'Add the team Google Drive folder containing all KTM files.', 'label' => 'Add team folder', 'href' => route('dashboard.team.index')]);
        }

        foreach ($registrations as $registration) {
            if ($registration->payment?->status === null) {
                $actions->push(['title' => 'Complete '.$registration->competition->code->value.' payment', 'description' => 'Add the proof to your team folder and confirm payment.', 'label' => 'Continue Payment', 'href' => route('dashboard.registration.payment', strtolower($registration->competition->code->value))]);
            } elseif ($registration->payment->status === PaymentStatus::Rejected) {
                $actions->push(['title' => $registration->competition->code->value.' payment rejected', 'description' => 'Correct the documents listed in the review note, then submit for review again.', 'label' => 'View Details', 'href' => route('dashboard.registration.payment', strtolower($registration->competition->code->value))]);
            }
        }

        return view('livewire.dashboard.database-overview', compact('user', 'team', 'registrations', 'actions', 'summitOrder'));
    }

    public function registrationTone(RegistrationStatus $status): string
    {
        return match ($status) {
            RegistrationStatus::Verified => 'success',
            RegistrationStatus::Rejected => 'error',
            default => 'warning',
        };
    }
}
