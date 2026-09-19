<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\VerifyEmailOtp;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationOtpController extends Controller
{
    public function __invoke(Request $request, VerifyEmailOtp $verifyEmailOtp): RedirectResponse
    {
        $validated = $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $verifyEmailOtp->handle($request->user(), $validated['otp']);

        return redirect()->route('dashboard.index')
            ->with('status', 'email-verified');
    }
}
