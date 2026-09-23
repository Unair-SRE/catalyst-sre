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
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
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
        $this->validate([
            'captainKtm' => ['required', 'file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:2048'],
        ]);

        $team = $this->teamOrFail();
        try {
            $uploadCaptainKtm->handle(Auth::user(), $team, $this->captainKtm);
        } catch (Throwable $exception) {
            report($exception);
            $this->addError('captainKtm', 'The KTM could not be uploaded. Please try again.');

            return;
        }

        $this->captainKtm = null;
        $this->feedback = 'Captain KTM uploaded.';
    }

    public function addMember(CreateTeamMemberWithKtm $createMember): void
    {
        $this->validate([
            'memberForm.name' => ['required', 'string', 'max:120'],
            'memberForm.email' => ['required', 'email', 'max:255'],
            'memberForm.whatsapp' => ['required', 'string', 'max:20'],
            'memberKtm' => ['required', 'file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:2048'],
        ]);

        try {
            $createMember->handle(Auth::user(), $this->teamOrFail(), $this->memberForm, $this->memberKtm);
        } catch (Throwable $exception) {
            report($exception);
            $this->addError('memberKtm', 'The member could not be saved. Check the email and KTM, then try again.');

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
        $this->validate([
            'editMemberForm.name' => ['required', 'string', 'max:120'],
            'editMemberForm.email' => ['required', 'email', 'max:255'],
            'editMemberForm.whatsapp' => ['required', 'string', 'max:20'],
            'editMemberKtm' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:2048'],
        ]);

        $member = $this->ownedMember($this->editingMemberId);
        try {
            $updateMember->handle(Auth::user(), $member, $this->editMemberForm, $this->editMemberKtm);
        } catch (Throwable $exception) {
            report($exception);
            $this->addError('editMemberKtm', 'The member could not be updated. Check the data and try again.');

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
}
