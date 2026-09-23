<?php

namespace App\Policies;

use App\Models\SummitOrder;
use App\Models\User;

class SummitOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, SummitOrder $order): bool
    {
        return $user->isAdmin() || $order->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasVerifiedEmail();
    }

    public function submitProof(User $user, SummitOrder $order): bool
    {
        return $user->hasVerifiedEmail() && $order->user_id === $user->id;
    }

    public function verify(User $user, SummitOrder $order): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, SummitOrder $order): bool
    {
        return false;
    }
}
