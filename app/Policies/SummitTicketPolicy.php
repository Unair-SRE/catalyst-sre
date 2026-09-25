<?php

namespace App\Policies;

use App\Models\SummitTicket;
use App\Models\User;

class SummitTicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, SummitTicket $ticket): bool
    {
        return $user->isAdmin() || $ticket->order->user_id === $user->id;
    }

    public function checkIn(User $user, SummitTicket $ticket): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, SummitTicket $ticket): bool
    {
        return false;
    }
}
