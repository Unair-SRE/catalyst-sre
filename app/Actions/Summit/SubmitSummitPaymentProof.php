<?php

namespace App\Actions\Summit;

use App\Contracts\SummitPaymentStorage;
use App\Enums\SummitOrderStatus;
use App\Enums\SummitTicketStatus;
use App\Models\SummitOrder;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class SubmitSummitPaymentProof
{
    public function __construct(private readonly SummitPaymentStorage $storage) {}

    public function handle(User $actor, SummitOrder $order, string $senderName, UploadedFile $proof): SummitOrder
    {
        Gate::forUser($actor)->authorize('submitProof', $order);

        $validated = Validator::make(
            ['sender_name' => $senderName, 'proof' => $proof],
            [
                'sender_name' => ['required', 'string', 'max:120'],
                'proof' => ['required', 'file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:5120'],
            ],
        )->validate();

        $currentOrder = $order->fresh();

        if ($currentOrder->payment_status !== SummitOrderStatus::WaitingPayment || $currentOrder->hasProof()) {
            throw ValidationException::withMessages([
                'proof' => 'Payment proof has already been submitted for this order.',
            ]);
        }

        $storedFile = $this->storage->upload($proof);

        try {
            return DB::transaction(function () use ($order, $storedFile, $validated): SummitOrder {
                $lockedOrder = SummitOrder::query()
                    ->with('tickets')
                    ->lockForUpdate()
                    ->findOrFail($order->id);

                if ($lockedOrder->payment_status !== SummitOrderStatus::WaitingPayment || $lockedOrder->hasProof()) {
                    throw ValidationException::withMessages([
                        'proof' => 'Payment proof has already been submitted for this order.',
                    ]);
                }

                $lockedOrder->update([
                    'sender_name' => trim($validated['sender_name']),
                    'payment_proof_url' => $storedFile->url,
                    'payment_proof_file_id' => $storedFile->fileId,
                    'payment_status' => SummitOrderStatus::WaitingVerification,
                ]);
                $lockedOrder->tickets()->update(['status' => SummitTicketStatus::WaitingVerification]);

                return $lockedOrder->fresh('tickets');
            });
        } catch (Throwable $exception) {
            try {
                $this->storage->delete($storedFile->fileId);
            } catch (Throwable $cleanupException) {
                report($cleanupException);
            }

            throw $exception;
        }
    }
}
