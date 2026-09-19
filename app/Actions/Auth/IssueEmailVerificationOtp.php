<?php

namespace App\Actions\Auth;

use App\Models\EmailVerificationOtp;
use App\Models\User;
use App\Notifications\VerifyEmailOtp;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class IssueEmailVerificationOtp
{
    public function handle(User $user): void
    {
        if ($user->hasVerifiedEmail()) {
            return;
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($user, $code): void {
            $otp = EmailVerificationOtp::query()
                ->whereBelongsTo($user)
                ->lockForUpdate()
                ->first();

            if ($otp?->last_sent_at->isAfter(now()->subSeconds(60))) {
                throw ValidationException::withMessages([
                    'otp' => 'Please wait 60 seconds before requesting another code.',
                ]);
            }

            EmailVerificationOtp::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'otp_hash' => Hash::make($code),
                    'expires_at' => now()->addMinutes(5),
                    'last_sent_at' => now(),
                    'consumed_at' => null,
                ],
            );
        });

        $user->notify(new VerifyEmailOtp($code));
    }
}
