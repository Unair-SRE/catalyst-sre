<?php

namespace App\Actions\Summit;

use App\Enums\SummitTicketStatus;
use App\Models\SummitTicket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CheckInTicket
{
    public function handle(User $admin, SummitTicket $ticket): SummitTicket
    {
        Gate::forUser($admin)->authorize('checkIn', $ticket);

        return DB::transaction(function () use ($admin, $ticket): SummitTicket {
            $lockedTicket = SummitTicket::query()
                ->with('order')
                ->lockForUpdate()
                ->findOrFail($ticket->id);

            if ($lockedTicket->status !== SummitTicketStatus::Active) {
                throw ValidationException::withMessages([
                    'ticket' => 'Only an active ticket can be checked in.',
                ]);
            }

            $checkedInAt = now();

            $lockedTicket->update([
                'status' => SummitTicketStatus::Used,
                'checked_in_by' => $admin->id,
                'checked_in_at' => $checkedInAt,
            ]);

            Log::info('Summit ticket checked in.', [
                'operation' => 'summit_ticket.check_in',
                'ticket_id' => $lockedTicket->id,
                'order_id' => $lockedTicket->summit_order_id,
                'admin_id' => $admin->id,
            ]);

            return $lockedTicket->fresh('order');
        });
    }
}
