<?php

namespace App\Actions\Auth;

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
            $lockedUser = User::query()
                ->lockForUpdate()
                ->findOrFail($user->id);

            if ($lockedUser->otp_hash
                && $lockedUser->otp_expires_at?->isAfter(now()->addMinutes(4))) {
                throw ValidationException::withMessages([
                    'otp' => 'Please wait 60 seconds before requesting another code.',
                ]);
            }

            $lockedUser->forceFill([
                'otp_hash' => Hash::make($code),
                'otp_expires_at' => now()->addMinutes(5),
            ])->save();
        });

        $user->notify(new VerifyEmailOtp($code));
    }
}
