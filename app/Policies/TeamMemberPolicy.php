<?php

namespace App\Policies;

use App\Models\TeamMember;
use App\Models\User;

class TeamMemberPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, TeamMember $teamMember): bool
    {
        return $user->isAdmin() || $teamMember->team->captain_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, TeamMember $teamMember): bool
    {
        return $user->isAdmin()
            || ($teamMember->team->captain_id === $user->id && ! $teamMember->team->isLocked());
    }

    public function delete(User $user, TeamMember $teamMember): bool
    {
        return $this->update($user, $teamMember);
    }
}
