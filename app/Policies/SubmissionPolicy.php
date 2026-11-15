<?php

namespace App\Policies;

use App\Models\Submission;
use App\Models\User;

class SubmissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Submission $submission): bool
    {
        return $user->isAdmin() || $submission->registration->team->captain_id === $user->id;
    }

    public function submit(User $user, Submission $submission): bool
    {
        return $user->hasVerifiedEmail() && $submission->registration->team->captain_id === $user->id;
    }

    public function delete(User $user, Submission $submission): bool
    {
        return false;
    }
}
