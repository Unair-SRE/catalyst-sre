<?php

namespace App\Actions\Payments;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class VerifyCompetitionPayment
{
    public function handle(User $admin, Payment $payment): Payment
    {
        Gate::forUser($admin)->authorize('verify', $payment);

        return DB::transaction(function () use ($admin, $payment): Payment {
            $lockedPayment = Payment::query()
                ->with('registration')
                ->lockForUpdate()
                ->findOrFail($payment->id);

            if ($lockedPayment->status === PaymentStatus::Verified) {
                return $lockedPayment;
            }

            if (! $lockedPayment->hasProof() || $lockedPayment->status === null) {
                throw ValidationException::withMessages([
                    'payment' => 'A payment proof must be submitted before verification.',
                ]);
            }

            $registration = $lockedPayment->registration()->lockForUpdate()->firstOrFail();
            $verifiedAt = now();

            $lockedPayment->update([
                'status' => PaymentStatus::Verified,
                'verified_by' => $admin->id,
                'verified_at' => $verifiedAt,
            ]);
            $registration->update(['status' => RegistrationStatus::Verified]);

            Log::info('Competition payment verified.', [
                'operation' => 'competition_payment.verify',
                'payment_id' => $lockedPayment->id,
                'registration_id' => $registration->id,
                'admin_id' => $admin->id,
            ]);

            return $lockedPayment->fresh(['registration', 'verifier']);
        });
    }
}
