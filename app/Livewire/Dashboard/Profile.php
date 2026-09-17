<?php

namespace App\Livewire\Dashboard;

use App\Support\Dashboard\DashboardProfileState;
use Illuminate\View\View;
use Livewire\Component;

class Profile extends Component
{
    /** @var array<string, string> */
    public array $profile = [];

    /** @var array<string, string> */
    public array $originalProfile = [];

    /** @var array<string, string> */
    public array $password = [
        'current' => '',
        'new' => '',
        'confirmation' => '',
    ];

    public ?string $profileFeedback = null;

    public ?string $passwordFeedback = null;

    public function mount(): void
    {
        $this->profile = app(DashboardProfileState::class)->get()['profile'];
        $this->originalProfile = $this->profile;
    }

    public function saveProfile(): void
    {
        $this->validate([
            'profile.name' => 'required|string|max:120',
            'profile.email' => 'required|email|max:160',
            'profile.whatsapp' => ['required', 'string', 'max:40', 'regex:/^[0-9+()\-\s]+$/'],
            'profile.institution' => 'required|string|max:160',
        ]);

        $this->originalProfile = $this->profile;
        $this->profileFeedback = 'Profile updated';
    }

    public function resetProfile(): void
    {
        $this->profile = $this->originalProfile;
        $this->profileFeedback = null;
        $this->resetValidation([
            'profile.name',
            'profile.email',
            'profile.whatsapp',
            'profile.institution',
        ]);
    }

    public function updatePassword(): void
    {
        $minimumLength = app(DashboardProfileState::class)->get()['password_policy']['minimum_length'];

        $this->validate([
            'password.current' => 'required|string',
            'password.new' => "required|string|min:{$minimumLength}|same:password.confirmation",
            'password.confirmation' => 'required|string',
        ], [
            'password.new.same' => 'The new password and confirmation must match.',
        ]);

        $this->password = [
            'current' => '',
            'new' => '',
            'confirmation' => '',
        ];
        $this->passwordFeedback = 'Password updated';
    }

    public function render(): View
    {
        return view('livewire.dashboard.profile', [
            'state' => app(DashboardProfileState::class)->get(),
        ]);
    }
}
