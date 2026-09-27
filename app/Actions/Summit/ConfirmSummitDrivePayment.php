<?php

namespace App\Actions\Summit;

use App\Enums\SummitOrderStatus;
use App\Enums\SummitTicketStatus;
use App\Models\SummitOrder;
use App\Models\User;
use App\Rules\GoogleDriveFolder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ConfirmSummitDrivePayment
{
    public function handle(
        User $actor,
        SummitOrder $order,
        string $senderName,
        string $driveUrl,
        bool $confirmed,
    ): SummitOrder {
        Gate::forUser($actor)->authorize('submitProof', $order);

        $data = Validator::make([
            'sender_name' => trim($senderName),
            'payment_drive_url' => trim($driveUrl),
            'confirmed' => $confirmed,
        ], [
            'sender_name' => ['required', 'string', 'max:120'],
            'payment_drive_url' => ['required', 'string', 'max:2048', new GoogleDriveFolder],
            'confirmed' => ['accepted'],
        ])->validate();

        return DB::transaction(function () use ($actor, $order, $data): SummitOrder {
            $lockedOrder = SummitOrder::query()
                ->with('tickets')
                ->lockForUpdate()
                ->findOrFail($order->id);

            Gate::forUser($actor)->authorize('submitProof', $lockedOrder);

            if ($lockedOrder->payment_status !== SummitOrderStatus::WaitingPayment || $lockedOrder->hasPaymentEvidence()) {
                throw ValidationException::withMessages([
                    'payment' => 'This Summit order has already been submitted for review.',
                ]);
            }

            $lockedOrder->update([
                'sender_name' => $data['sender_name'],
                'payment_drive_url' => $data['payment_drive_url'],
                'payment_submitted_at' => now(),
                'payment_status' => SummitOrderStatus::WaitingVerification,
                'review_note' => null,
                'verified_by' => null,
                'verified_at' => null,
            ]);
            $lockedOrder->tickets()->update(['status' => SummitTicketStatus::WaitingVerification]);

            Log::info('Summit Drive payment submitted for review.', [
                'operation' => 'summit_order.submit_drive_payment',
                'order_id' => $lockedOrder->id,
                'actor_id' => $actor->id,
            ]);

            return $lockedOrder->fresh('tickets');
        });
    }
}
