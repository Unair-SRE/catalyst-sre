<?php

namespace App\Livewire\Dashboard;

use App\Actions\Teams\CreateTeam;
use App\Actions\Teams\CreateTeamMemberWithKtm;
use App\Actions\Teams\DeleteTeamMemberWithKtm;
use App\Actions\Teams\UpdateTeam;
use App\Actions\Teams\UpdateTeamMemberWithKtm;
use App\Actions\Teams\UploadCaptainKtm;
use App\Models\Team;
use App\Models\TeamMember;
use App\Rules\AvailableTeamEmail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use RuntimeException;
use Throwable;

class TeamManagement extends Component
{
    use WithFileUploads;

    /** @var array<string, string> */
    public array $teamForm = ['name' => '', 'institution' => ''];

    /** @var array<string, string> */
    public array $memberForm = ['name' => '', 'email' => '', 'whatsapp' => ''];

    /** @var array<string, string> */
    public array $editMemberForm = ['name' => '', 'email' => '', 'whatsapp' => ''];

    public ?TemporaryUploadedFile $captainKtm = null;

    public ?TemporaryUploadedFile $memberKtm = null;

    public ?TemporaryUploadedFile $editMemberKtm = null;

    public ?int $editingMemberId = null;

    public ?string $feedback = null;

    public function mount(): void
    {
        $this->fillTeamForm();
    }

    public function saveTeam(CreateTeam $createTeam, UpdateTeam $updateTeam): void
    {
        $this->feedback = null;

        $this->validate([
            'teamForm.name' => ['required', 'string', 'max:120'],
            'teamForm.institution' => ['required', 'string', 'max:160'],
        ]);

        $team = $this->team();

        if ($team) {
            $updateTeam->handle(Auth::user(), $team, $this->teamForm);
            $this->feedback = 'Team details updated.';
        } else {
            $createTeam->handle(Auth::user(), $this->teamForm);
            $this->feedback = 'Team created. Upload the captain KTM and add members before registering.';
        }

        $this->fillTeamForm();
    }

    public function uploadCaptainKtm(UploadCaptainKtm $uploadCaptainKtm): void
    {
        $this->feedback = null;

        $this->validate([
            'captainKtm' => $this->ktmRules(),
        ], $this->ktmMessages('captainKtm'));

        $team = $this->teamOrFail();

        try {
            $uploadCaptainKtm->handle(Auth::user(), $team, $this->captainKtm);
        } catch (ValidationException $exception) {
            $this->copyValidationErrors($exception, ['ktm' => 'captainKtm']);

            return;
        } catch (RuntimeException $exception) {
            report($exception);
            $this->addError('captainKtm', $this->storageFailureMessage($exception));

            return;
        } catch (Throwable $exception) {
            report($exception);
            $this->addError('captainKtm', 'The KTM upload could not be completed. No data was changed. Please try again.');

            return;
        }

        $this->captainKtm = null;
        $this->feedback = 'Captain KTM uploaded.';
    }

    public function addMember(CreateTeamMemberWithKtm $createMember): void
    {
        $this->feedback = null;

        $this->validate([
            'memberForm.name' => ['required', 'string', 'max:120'],
            'memberForm.email' => ['required', 'email', 'max:255', new AvailableTeamEmail],
            'memberForm.whatsapp' => ['required', 'string', 'max:20'],
            'memberKtm' => $this->ktmRules(),
        ], [
            ...$this->memberMessages('memberForm'),
            ...$this->ktmMessages('memberKtm'),
        ]);

        try {
            $createMember->handle(Auth::user(), $this->teamOrFail(), $this->memberForm, $this->memberKtm);
        } catch (ValidationException $exception) {
            $this->copyValidationErrors($exception, [
                'name' => 'memberForm.name',
                'email' => 'memberForm.email',
                'whatsapp' => 'memberForm.whatsapp',
                'ktm' => 'memberKtm',
                'team' => 'memberForm',
                'members' => 'memberForm',
            ]);

            return;
        } catch (RuntimeException $exception) {
            report($exception);
            $this->addError('memberKtm', $this->storageFailureMessage($exception));

            return;
        } catch (Throwable $exception) {
            report($exception);
            $this->addError('memberForm', 'The member could not be saved. No team data was changed. Please try again.');

            return;
        }

        $this->resetMemberForm();
        $this->feedback = 'Team member added.';
    }

    public function startEditingMember(int $memberId): void
    {
        $member = $this->ownedMember($memberId);

        $this->editingMemberId = $member->id;
        $this->editMemberForm = $member->only(['name', 'email', 'whatsapp']);
        $this->editMemberKtm = null;
        $this->resetValidation();
    }

    public function cancelEditingMember(): void
    {
        $this->editingMemberId = null;
        $this->editMemberKtm = null;
        $this->editMemberForm = ['name' => '', 'email' => '', 'whatsapp' => ''];
        $this->resetValidation();
    }

    public function updateMember(UpdateTeamMemberWithKtm $updateMember): void
    {
        $this->feedback = null;
        $member = $this->ownedMember($this->editingMemberId);

        $this->validate([
            'editMemberForm.name' => ['required', 'string', 'max:120'],
            'editMemberForm.email' => ['required', 'email', 'max:255', new AvailableTeamEmail($member->id)],
            'editMemberForm.whatsapp' => ['required', 'string', 'max:20'],
            'editMemberKtm' => $this->ktmRules(nullable: true),
        ], [
            ...$this->memberMessages('editMemberForm'),
            ...$this->ktmMessages('editMemberKtm'),
        ]);

        try {
            $updateMember->handle(Auth::user(), $member, $this->editMemberForm, $this->editMemberKtm);
        } catch (ValidationException $exception) {
            $this->copyValidationErrors($exception, [
                'name' => 'editMemberForm.name',
                'email' => 'editMemberForm.email',
                'whatsapp' => 'editMemberForm.whatsapp',
                'ktm' => 'editMemberKtm',
                'team' => 'editMemberForm',
                'members' => 'editMemberForm',
            ]);

            return;
        } catch (RuntimeException $exception) {
            report($exception);
            $this->addError('editMemberKtm', $this->storageFailureMessage($exception));

            return;
        } catch (Throwable $exception) {
            report($exception);
            $this->addError('editMemberForm', 'The member could not be updated. No team data was changed. Please try again.');

            return;
        }

        $this->cancelEditingMember();
        $this->feedback = 'Team member updated.';
    }

    public function removeMember(int $memberId, DeleteTeamMemberWithKtm $deleteMember): void
    {
        $deleteMember->handle(Auth::user(), $this->ownedMember($memberId));
        $this->cancelEditingMember();
        $this->feedback = 'Team member removed.';
    }

    public function updatedCaptainKtm(): void
    {
        $this->feedback = null;

        $this->validateOnly('captainKtm', [
            'captainKtm' => $this->ktmRules(),
        ], $this->ktmMessages('captainKtm'));
    }

    public function updatedMemberKtm(): void
    {
        $this->feedback = null;

        $this->validateOnly('memberKtm', [
            'memberKtm' => $this->ktmRules(),
        ], $this->ktmMessages('memberKtm'));
    }

    public function updatedEditMemberKtm(): void
    {
        $this->feedback = null;

        $this->validateOnly('editMemberKtm', [
            'editMemberKtm' => $this->ktmRules(nullable: true),
        ], $this->ktmMessages('editMemberKtm'));
    }

    public function render(): View
    {
        return view('livewire.dashboard.team-management', [
            'team' => $this->team()?->load(['captain', 'members']),
        ]);
    }

    private function fillTeamForm(): void
    {
        $team = $this->team();

        if ($team) {
            $this->teamForm = $team->only(['name', 'institution']);
        }
    }

    private function team(): ?Team
    {
        return Auth::user()->captainedTeam()->first();
    }

    private function teamOrFail(): Team
    {
        return Auth::user()->captainedTeam()->firstOrFail();
    }

    private function ownedMember(?int $memberId): TeamMember
    {
        abort_if($memberId === null, 404);

        return $this->teamOrFail()->members()->findOrFail($memberId);
    }

    private function resetMemberForm(): void
    {
        $this->memberForm = ['name' => '', 'email' => '', 'whatsapp' => ''];
        $this->memberKtm = null;
        $this->resetValidation();
    }

    /** @return array<int, string> */
    private function ktmRules(bool $nullable = false): array
    {
        return [
            $nullable ? 'nullable' : 'required',
            'file',
            'mimes:jpg,jpeg,png',
            'mimetypes:image/jpeg,image/png',
            'max:2048',
        ];
    }

    /** @return array<string, string> */
    private function ktmMessages(string $field): array
    {
        return [
            $field.'.required' => 'Select a KTM image before continuing.',
            $field.'.file' => 'The selected KTM must be a valid file.',
            $field.'.mimes' => 'The KTM must be a JPG, JPEG, or PNG image.',
            $field.'.mimetypes' => 'The KTM must be a JPG, JPEG, or PNG image.',
            $field.'.max' => 'The KTM must not exceed 2 MB.',
        ];
    }

    /** @return array<string, string> */
    private function memberMessages(string $form): array
    {
        return [
            $form.'.name.required' => 'Enter the member legal name.',
            $form.'.email.required' => 'Enter the member email address.',
            $form.'.email.email' => 'Enter a valid member email address.',
            $form.'.whatsapp.required' => 'Enter the member WhatsApp number.',
        ];
    }

    /** @param array<string, string> $fieldMap */
    private function copyValidationErrors(ValidationException $exception, array $fieldMap): void
    {
        foreach ($exception->errors() as $field => $messages) {
            $target = $fieldMap[$field] ?? $field;

            foreach ($messages as $message) {
                $this->addError($target, $message);
            }
        }
    }

    private function storageFailureMessage(RuntimeException $exception): string
    {
        if (str_contains($exception->getMessage(), 'is not configured')) {
            return 'KTM storage is not configured. Contact the Catalyst administrator before trying again.';
        }

        return 'The KTM upload could not be completed. No data was saved. Please try again.';
    }
}
