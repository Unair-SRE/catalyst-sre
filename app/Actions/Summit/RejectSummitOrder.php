<?php

namespace App\Actions\Summit;

use App\Enums\SummitOrderStatus;
use App\Enums\SummitTicketStatus;
use App\Models\SummitOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class RejectSummitOrder
{
    public function handle(User $admin, SummitOrder $order): SummitOrder
    {
        Gate::forUser($admin)->authorize('verify', $order);

        return DB::transaction(function () use ($admin, $order): SummitOrder {
            $lockedOrder = SummitOrder::query()
                ->with('tickets')
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($lockedOrder->payment_status === SummitOrderStatus::Rejected) {
                return $lockedOrder;
            }

            $lockedOrder->update([
                'payment_status' => SummitOrderStatus::Rejected,
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);
            $lockedOrder->tickets()
                ->whereIn('status', [SummitTicketStatus::WaitingPayment, SummitTicketStatus::WaitingVerification])
                ->update(['status' => SummitTicketStatus::Rejected]);

            Log::info('Summit order rejected.', [
                'operation' => 'summit_order.reject',
                'order_id' => $lockedOrder->id,
                'admin_id' => $admin->id,
            ]);

            return $lockedOrder->fresh('tickets');
        });
    }
}
