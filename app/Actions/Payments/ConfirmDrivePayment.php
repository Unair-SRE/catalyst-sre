<?php

namespace App\Actions\Payments;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Payment;
use App\Models\Team;
use App\Models\User;
use App\Rules\GoogleDriveFolder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ConfirmDrivePayment
{
    public function handle(User $actor, Payment $payment, string $senderName, bool $confirmed): Payment
    {
        Gate::forUser($actor)->authorize('submit', $payment);
        $data = Validator::make(['sender_name' => trim($senderName), 'confirmed' => $confirmed], [
            'sender_name' => ['required', 'string', 'max:120'],
            'confirmed' => ['accepted'],
        ])->validate();

        return DB::transaction(function () use ($actor, $payment, $data): Payment {
            $team = Team::query()->lockForUpdate()->findOrFail($payment->registration->team_id);
            $payment = Payment::query()->lockForUpdate()->findOrFail($payment->id);
            Gate::forUser($actor)->authorize('submit', $payment);
            if (! GoogleDriveFolder::valid($team->documents_drive_url)) {
                throw ValidationException::withMessages(['documents' => 'Add your team Google Drive folder before confirming payment.']);
            }
            if ($payment->status !== null && $payment->status !== PaymentStatus::Rejected) {
                throw ValidationException::withMessages(['payment' => 'This payment has already been submitted for review.']);
            }
            $team->lock();
            $payment->update([
                'sender_name' => $data['sender_name'],
                'documents_submitted_at' => now(),
                'status' => PaymentStatus::WaitingVerification,
                'review_note' => null,
                'verified_by' => null,
                'verified_at' => null,
            ]);
            $payment->registration()->update(['status' => RegistrationStatus::Pending]);
            Log::info('Team Drive documents submitted for review.', [
                'payment_id' => $payment->id, 'team_id' => $team->id, 'actor_id' => $actor->id,
            ]);

            return $payment->fresh(['registration.team', 'registration.competition']);
        });
    }
}
