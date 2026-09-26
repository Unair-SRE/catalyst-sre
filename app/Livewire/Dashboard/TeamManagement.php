<?php

namespace App\Livewire\Dashboard;

use App\Actions\Teams\CreateDriveTeam;
use App\Actions\Teams\RemoveTeamMember;
use App\Actions\Teams\SaveDriveTeamMember;
use App\Actions\Teams\UpdateDriveFolder;
use App\Actions\Teams\UpdateTeam;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

class TeamManagement extends Component
{
    public array $teamForm = ['name' => '', 'institution' => ''];

    public string $documentsDriveUrl = '';

    public array $setupMembers = [];

    public array $memberForm = ['name' => '', 'email' => '', 'whatsapp' => ''];

    public array $editMemberForm = ['name' => '', 'email' => '', 'whatsapp' => ''];

    public ?int $editingMemberId = null;

    public ?string $feedback = null;

    public function mount(): void
    {
        if ($team = $this->team()) {
            $this->teamForm = $team->only(['name', 'institution']);
            $this->documentsDriveUrl = $team->documents_drive_url ?? '';
        }
    }

    public function createTeam(CreateDriveTeam $action): void
    {
        try {
            $action->handle(Auth::user(), [...$this->teamForm, 'documents_drive_url' => trim($this->documentsDriveUrl)], $this->setupMembers);
        } catch (ValidationException $exception) {
            $this->copyErrors($exception);

            return;
        }
        $this->setupMembers = [];
        $this->resetValidation();
        $this->feedback = 'Team created. Your folder will be checked by the committee.';
    }

    public function addSetupMember(): void
    {
        if (count($this->setupMembers) < 2) {
            $this->setupMembers[] = ['name' => '', 'email' => '', 'whatsapp' => ''];
        }
    }

    public function removeSetupMember(int $index): void
    {
        if (array_key_exists($index, $this->setupMembers)) {
            array_splice($this->setupMembers, $index, 1);
            $this->resetValidation();
        }
    }

    public function saveTeam(UpdateTeam $action): void
    {
        $this->validate([
            'teamForm.name' => ['required', 'string', 'max:120'],
            'teamForm.institution' => ['required', 'string', 'max:160'],
        ]);
        $action->handle(Auth::user(), $this->teamOrFail(), $this->teamForm);
        $this->feedback = 'Team details updated.';
    }

    public function saveFolder(UpdateDriveFolder $action): void
    {
        try {
            $action->handle(Auth::user(), $this->teamOrFail(), trim($this->documentsDriveUrl));
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $messages) {
                $this->addError('documentsDriveUrl', $messages[0]);
            }

            return;
        }
        $this->resetValidation();
        $this->feedback = 'Team folder saved. Sharing permissions and documents will be checked by the committee.';
    }

    public function addMember(SaveDriveTeamMember $action): void
    {
        try {
            $action->handle(Auth::user(), $this->teamOrFail(), $this->memberForm);
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                $this->addError('memberForm.'.$field, $messages[0]);
            }

            return;
        }
        $this->memberForm = ['name' => '', 'email' => '', 'whatsapp' => ''];
        $this->resetValidation();
        $this->feedback = 'Member added. Remember to add their KTM to the team folder.';
    }

    public function startEditingMember(int $memberId): void
    {
        $team = $this->teamOrFail();
        $this->authorize('manageMembers', $team);
        $member = $team->members()->findOrFail($memberId);
        $this->editingMemberId = $member->id;
        $this->editMemberForm = $member->only(['name', 'email', 'whatsapp']);
    }

    public function cancelEditingMember(): void
    {
        $this->editingMemberId = null;
        $this->editMemberForm = ['name' => '', 'email' => '', 'whatsapp' => ''];
        $this->resetValidation();
    }

    public function updateMember(SaveDriveTeamMember $action): void
    {
        abort_if($this->editingMemberId === null, 404);
        try {
            $action->handle(Auth::user(), $this->teamOrFail(), $this->editMemberForm, $this->editingMemberId);
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                $this->addError('editMemberForm.'.$field, $messages[0]);
            }

            return;
        }
        $this->cancelEditingMember();
        $this->feedback = 'Member updated.';
    }

    public function removeMember(int $memberId, RemoveTeamMember $action): void
    {
        $action->handle(Auth::user(), $this->teamOrFail()->members()->findOrFail($memberId));
        $this->cancelEditingMember();
        $this->feedback = 'Member removed. Update the contents of your team folder if necessary.';
    }

    public function render(): View
    {
        return view('livewire.dashboard.team-management', ['team' => $this->team()?->load(['captain', 'members'])]);
    }

    private function team(): ?Team
    {
        return Auth::user()->captainedTeam()->first();
    }

    private function teamOrFail(): Team
    {
        return Auth::user()->captainedTeam()->firstOrFail();
    }

    private function copyErrors(ValidationException $exception): void
    {
        foreach ($exception->errors() as $field => $messages) {
            $key = match (true) {
                $field === 'team.documents_drive_url' => 'documentsDriveUrl',
                str_starts_with($field, 'team.') => 'teamForm.'.substr($field, 5),
                str_starts_with($field, 'members.') => 'setupMembers.'.substr($field, 8),
                default => 'setup',
            };
            $this->addError($key, $messages[0]);
        }
    }
}
