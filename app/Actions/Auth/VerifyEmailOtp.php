<?php

namespace App\Actions\Auth;

use App\Models\EmailVerificationOtp;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class VerifyEmailOtp
{
    public function handle(User $user, string $code): void
    {
        DB::transaction(function () use ($user, $code): void {
            $otp = EmailVerificationOtp::query()
                ->whereBelongsTo($user)
                ->lockForUpdate()
                ->first();

            if (! $otp || $otp->consumed_at || $otp->expires_at->isPast()) {
                throw ValidationException::withMessages([
                    'otp' => 'The verification code is invalid or has expired.',
                ]);
            }

            if (! Hash::check($code, $otp->otp_hash)) {
                throw ValidationException::withMessages([
                    'otp' => 'The verification code is invalid or has expired.',
                ]);
            }

            $otp->forceFill(['consumed_at' => now()])->save();
            $user->markEmailAsVerified();
        });
    }
}
