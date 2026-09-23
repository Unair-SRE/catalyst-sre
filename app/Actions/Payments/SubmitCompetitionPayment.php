<?php

namespace App\Actions\Payments;

use App\Contracts\CompetitionPaymentStorage;
use App\Enums\PaymentStatus;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class SubmitCompetitionPayment
{
    public function __construct(private readonly CompetitionPaymentStorage $storage) {}

    public function handle(User $actor, Payment $payment, string $senderName, UploadedFile $proof): Payment
    {
        Gate::forUser($actor)->authorize('submit', $payment);

        $validated = Validator::make(
            ['sender_name' => $senderName, 'proof' => $proof],
            [
                'sender_name' => ['required', 'string', 'max:120'],
                'proof' => ['required', 'file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:2048'],
            ],
        )->validate();

        $currentPayment = $payment->fresh();

        if ($currentPayment->status !== null || $currentPayment->hasProof()) {
            throw ValidationException::withMessages([
                'proof' => 'Payment proof has already been submitted. Contact the Catalyst contact person for corrections.',
            ]);
        }

        $storedFile = $this->storage->upload($proof);

        try {
            return DB::transaction(function () use ($payment, $storedFile, $validated): Payment {
                $lockedPayment = Payment::query()
                    ->with('registration.team')
                    ->lockForUpdate()
                    ->findOrFail($payment->id);

                if ($lockedPayment->status !== null || $lockedPayment->hasProof()) {
                    throw ValidationException::withMessages([
                        'proof' => 'Payment proof has already been submitted. Contact the Catalyst contact person for corrections.',
                    ]);
                }

                $lockedPayment->registration->team()->lockForUpdate()->firstOrFail()->lock();

                $lockedPayment->update([
                    'sender_name' => trim($validated['sender_name']),
                    'payment_proof_url' => $storedFile->url,
                    'payment_proof_file_id' => $storedFile->fileId,
                    'status' => PaymentStatus::WaitingVerification,
                    'verified_by' => null,
                    'verified_at' => null,
                ]);

                return $lockedPayment->fresh(['registration.team', 'registration.competition']);
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
