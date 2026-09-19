<?php

namespace App\Livewire\Dashboard;

use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Support\Dashboard\DashboardProfileState;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
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
            'profile.whatsapp' => ['required', 'string', 'max:20', 'regex:/^[0-9+()\-\s]+$/'],
        ]);

        try {
            app(UpdateUserProfileInformation::class)->update(Auth::user(), [
                'name' => $this->profile['name'],
                'email' => $this->profile['email'],
                'whatsapp' => $this->profile['whatsapp'],
            ]);
        } catch (ValidationException $exception) {
            $this->addProfileErrors($exception);

            return;
        }

        $this->profile = app(DashboardProfileState::class)->get()['profile'];
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
        ]);
    }

    public function updatePassword(): void
    {
        $this->validate([
            'password.current' => 'required|string',
            'password.new' => 'required|string|min:8|same:password.confirmation',
            'password.confirmation' => 'required|string',
        ], [
            'password.new.same' => 'The new password and confirmation must match.',
        ]);

        try {
            app(UpdateUserPassword::class)->update(Auth::user(), [
                'current_password' => $this->password['current'],
                'password' => $this->password['new'],
                'password_confirmation' => $this->password['confirmation'],
            ]);
        } catch (ValidationException $exception) {
            $this->addPasswordErrors($exception);

            return;
        }

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

    private function addProfileErrors(ValidationException $exception): void
    {
        foreach ($exception->errors() as $field => $messages) {
            $this->addError("profile.{$field}", $messages[0]);
        }
    }

    private function addPasswordErrors(ValidationException $exception): void
    {
        $fields = [
            'current_password' => 'password.current',
            'password' => 'password.new',
        ];

        foreach ($exception->errors() as $field => $messages) {
            $this->addError($fields[$field] ?? $field, $messages[0]);
        }
    }
}
