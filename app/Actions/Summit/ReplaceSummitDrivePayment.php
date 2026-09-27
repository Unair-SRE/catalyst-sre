<?php

namespace App\Actions\Summit;

use App\Enums\SummitOrderStatus;
use App\Enums\SummitTicketStatus;
use App\Models\SummitOrder;
use App\Models\User;
use App\Rules\GoogleDriveFolder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ReplaceSummitDrivePayment
{
    public function handle(User $admin, SummitOrder $order, string $driveUrl): SummitOrder
    {
        Gate::forUser($admin)->authorize('verify', $order);

        $data = Validator::make(['payment_drive_url' => trim($driveUrl)], [
            'payment_drive_url' => ['required', 'string', 'max:2048', new GoogleDriveFolder],
        ])->validate();

        return DB::transaction(function () use ($order, $data): SummitOrder {
            $lockedOrder = SummitOrder::query()->with('tickets')->lockForUpdate()->findOrFail($order->id);

            if ($lockedOrder->payment_status !== SummitOrderStatus::Rejected) {
                throw ValidationException::withMessages([
                    'payment_drive_url' => 'Only a rejected order can receive a corrected payment folder.',
                ]);
            }

            $lockedOrder->update([
                'payment_drive_url' => $data['payment_drive_url'],
                'payment_submitted_at' => now(),
                'payment_status' => SummitOrderStatus::WaitingVerification,
                'review_note' => null,
                'verified_by' => null,
                'verified_at' => null,
            ]);
            $lockedOrder->tickets()->update(['status' => SummitTicketStatus::WaitingVerification]);

            return $lockedOrder->fresh('tickets');
        });
    }
}
