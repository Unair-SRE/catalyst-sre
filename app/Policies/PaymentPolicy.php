<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Payment $payment): bool
    {
        return $user->isAdmin() || $payment->registration->team->captain_id === $user->id;
    }

    public function submit(User $user, Payment $payment): bool
    {
        return $user->isParticipant()
            && $user->hasVerifiedEmail()
            && $payment->registration->team->captain_id === $user->id;
    }

    public function verify(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }

    public function replaceProof(User $user, Payment $payment): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Payment $payment): bool
    {
        return false;
    }
}
