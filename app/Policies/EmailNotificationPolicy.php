<?php

namespace App\Policies;

use App\Models\EmailNotification;
use App\Models\User;

class EmailNotificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, EmailNotification $notification): bool
    {
        return $user->isAdmin();
    }

    public function retry(User $user, EmailNotification $notification): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, EmailNotification $notification): bool
    {
        return false;
    }
}
