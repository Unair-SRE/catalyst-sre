<?php

namespace App\Policies;

use App\Models\Team;
use App\Models\User;

class TeamPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Team $team): bool
    {
        return $user->isAdmin() || $team->captain_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isParticipant()
            && $user->hasVerifiedEmail()
            && ! $user->captainedTeam()->exists();
    }

    public function update(User $user, Team $team): bool
    {
        return $user->isAdmin()
            || ($team->captain_id === $user->id && ! $team->isLocked());
    }

    public function manageMembers(User $user, Team $team): bool
    {
        return $this->update($user, $team);
    }

    public function delete(User $user, Team $team): bool
    {
        return false;
    }
}
