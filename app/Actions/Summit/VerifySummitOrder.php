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

        $freshOrder = $order->fresh('tickets');

        if ($freshOrder->payment_status === SummitOrderStatus::Verified) {
            return $freshOrder;
        }

        if (! $freshOrder->hasPaymentEvidence() || $freshOrder->payment_status !== SummitOrderStatus::WaitingVerification) {
            throw ValidationException::withMessages([
                'order' => 'A Google Drive payment folder must be submitted before verification.',
            ]);
        }

        return DB::transaction(function () use ($admin, $order): SummitOrder {
            $lockedOrder = SummitOrder::query()
                ->with('tickets')
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($lockedOrder->payment_status === SummitOrderStatus::Verified) {
                return $lockedOrder;
            }

            if (! $lockedOrder->hasPaymentEvidence() || $lockedOrder->payment_status !== SummitOrderStatus::WaitingVerification) {
                throw ValidationException::withMessages([
                    'order' => 'A Google Drive payment folder must be waiting for verification before approval.',
                ]);
            }

            $lockedOrder->update([
                'payment_status' => SummitOrderStatus::Verified,
                'verified_by' => $admin->id,
                'verified_at' => now(),
            ]);
            $lockedOrder->tickets()->update([
                'status' => SummitTicketStatus::Active,
                'pdf_url' => null,
                'pdf_file_id' => null,
            ]);

            Log::info('Summit order verified.', [
                'operation' => 'summit_order.verify',
                'order_id' => $lockedOrder->id,
                'admin_id' => $admin->id,
            ]);

            return $lockedOrder->fresh('tickets');
        });
    }
}
