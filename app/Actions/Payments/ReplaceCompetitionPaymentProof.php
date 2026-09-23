<?php

namespace App\Actions\Payments;

use App\Contracts\CompetitionPaymentStorage;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;

class ReplaceCompetitionPaymentProof
{
    public function __construct(private readonly CompetitionPaymentStorage $storage) {}

    public function handle(User $admin, Payment $payment, string $senderName, UploadedFile $proof): Payment
    {
        Gate::forUser($admin)->authorize('replaceProof', $payment);

        $validated = Validator::make(
            ['sender_name' => $senderName, 'proof' => $proof],
            [
                'sender_name' => ['required', 'string', 'max:120'],
                'proof' => ['required', 'file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:10240'],
            ],
        )->validate();

        if ($payment->fresh()->status !== PaymentStatus::Rejected) {
            throw ValidationException::withMessages([
                'payment' => 'Only a rejected payment proof can be replaced by an administrator.',
            ]);
        }

        $storedFile = $this->storage->upload($proof);

        try {
            return DB::transaction(function () use ($admin, $payment, $storedFile, $validated): Payment {
                $lockedPayment = Payment::query()
                    ->with('registration')
                    ->lockForUpdate()
                    ->findOrFail($payment->id);
                $oldFileId = $lockedPayment->payment_proof_file_id;

                if ($lockedPayment->status !== PaymentStatus::Rejected) {
                    throw ValidationException::withMessages([
                        'payment' => 'Only a rejected payment proof can be replaced by an administrator.',
                    ]);
                }

                $lockedPayment->update([
                    'sender_name' => trim($validated['sender_name']),
                    'payment_proof_url' => $storedFile->url,
                    'payment_proof_file_id' => $storedFile->fileId,
                    'status' => PaymentStatus::WaitingVerification,
                    'verified_by' => null,
                    'verified_at' => null,
                ]);
                $lockedPayment->registration()->update(['status' => RegistrationStatus::Pending]);

                Log::info('Competition payment proof replaced by administrator.', [
                    'operation' => 'competition_payment.replace_proof',
                    'payment_id' => $lockedPayment->id,
                    'registration_id' => $lockedPayment->registration_id,
                    'admin_id' => $admin->id,
                    'old_file_id' => $oldFileId,
                    'new_file_id' => $storedFile->fileId,
                ]);

                if (filled($oldFileId)) {
                    DB::afterCommit(function () use ($oldFileId): void {
                        try {
                            $this->storage->delete($oldFileId);
                        } catch (Throwable $exception) {
                            report($exception);
                        }
                    });
                }

                return $lockedPayment->fresh(['registration', 'registration.team', 'registration.competition']);
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
