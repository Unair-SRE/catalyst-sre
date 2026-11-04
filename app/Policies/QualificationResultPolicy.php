<?php

namespace App\Policies;

use App\Models\QualificationResult;
use App\Models\User;

class QualificationResultPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, QualificationResult $result): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, QualificationResult $result): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, QualificationResult $result): bool
    {
        return false;
    }
}
