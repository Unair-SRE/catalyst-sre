<?php

namespace App\Actions\Summit;

use App\Enums\SummitOrderStatus;
use App\Enums\SummitTicketStatus;
use App\Models\SummitOrder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class VerifySummitOrder
{
    public function handle(User $admin, SummitOrder $order): SummitOrder
    {
        Gate::forUser($admin)->authorize('verify', $order);

        return DB::transaction(function () use ($admin, $order): SummitOrder {
            $lockedOrder = SummitOrder::query()
                ->with('tickets')
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($lockedOrder->payment_status === SummitOrderStatus::Verified) {
                return $lockedOrder;
            }

            if (! $lockedOrder->hasProof() || $lockedOrder->payment_status !== SummitOrderStatus::WaitingVerification) {
                throw ValidationException::withMessages([
                    'order' => 'A payment proof must be submitted before verification.',
                ]);
            }

            $lockedOrder->update([
                'payment_status' => SummitOrderStatus::Verified,
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);
            $lockedOrder->tickets()
                ->where('status', SummitTicketStatus::WaitingVerification)
                ->update(['status' => SummitTicketStatus::Active]);

            Log::info('Summit order verified.', [
                'operation' => 'summit_order.verify',
                'order_id' => $lockedOrder->id,
                'admin_id' => $admin->id,
            ]);

            return $lockedOrder->fresh('tickets');
        });
    }
}
