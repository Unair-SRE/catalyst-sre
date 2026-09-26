<?php

namespace App\Actions\Payments;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Payment;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class RejectCompetitionPayment
{
    public function handle(User $admin, Payment $payment, ?string $reviewNote = null): Payment
    {
        Gate::forUser($admin)->authorize('verify', $payment);

        if ($reviewNote !== null) {
            Validator::make(['review_note' => trim($reviewNote)], ['review_note' => ['required', 'string', 'max:2000']])->validate();
        }

        return DB::transaction(function () use ($admin, $payment, $reviewNote): Payment {
            Team::query()->lockForUpdate()->findOrFail($payment->registration->team_id);
            $lockedPayment = Payment::query()
                ->with('registration')
                ->lockForUpdate()
                ->findOrFail($payment->id);

            if ($lockedPayment->status === PaymentStatus::Rejected) {
                return $lockedPayment;
            }

            if ($lockedPayment->status !== PaymentStatus::WaitingVerification) {
                throw ValidationException::withMessages([
                    'payment' => 'Only a payment waiting for verification can be rejected.',
                ]);
            }

            if ($lockedPayment->documents_submitted_at && blank($reviewNote)) {
                throw ValidationException::withMessages(['review_note' => 'Explain which documents or permissions must be corrected.']);
            }

            $registration = $lockedPayment->registration()->lockForUpdate()->firstOrFail();
            $verifiedAt = now();

            $lockedPayment->update([
                'status' => PaymentStatus::Rejected,
                'review_note' => $reviewNote === null ? null : trim($reviewNote),
                'verified_by' => $admin->id,
                'verified_at' => $verifiedAt,
            ]);
            $registration->update(['status' => RegistrationStatus::Rejected]);

            Log::info('Competition payment rejected.', [
                'operation' => 'competition_payment.reject',
                'payment_id' => $lockedPayment->id,
                'registration_id' => $registration->id,
                'admin_id' => $admin->id,
            ]);

            return $lockedPayment->fresh(['registration', 'verifier']);
        });
    }
}
