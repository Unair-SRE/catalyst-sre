<?php

namespace App\Policies;

use App\Models\CompetitionRound;
use App\Models\User;

class CompetitionRoundPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, CompetitionRound $round): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, CompetitionRound $round): bool
    {
        return $user->isAdmin();
    }

    public function publish(User $user, CompetitionRound $round): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, CompetitionRound $round): bool
    {
        return false;
    }
}
