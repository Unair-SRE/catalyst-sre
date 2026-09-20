<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class VerifyEmailOtp
{
    public function handle(User $user, string $code): void
    {
        DB::transaction(function () use ($user, $code): void {
            $lockedUser = User::query()
                ->lockForUpdate()
                ->findOrFail($user->id);

            if (! $lockedUser->otp_hash
                || ! $lockedUser->otp_expires_at
                || $lockedUser->otp_expires_at->isPast()) {
                throw ValidationException::withMessages([
                    'otp' => 'The verification code is invalid or has expired.',
                ]);
            }

            if (! Hash::check($code, $lockedUser->otp_hash)) {
                throw ValidationException::withMessages([
                    'otp' => 'The verification code is invalid or has expired.',
                ]);
            }

            $lockedUser->forceFill([
                'email_verified_at' => now(),
                'otp_hash' => null,
                'otp_expires_at' => null,
            ])->save();
        });
    }
}
