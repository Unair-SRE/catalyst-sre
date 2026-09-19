<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\IssueEmailVerificationOtp;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    public function __invoke(Request $request, IssueEmailVerificationOtp $issueOtp): RedirectResponse
    {
        $issueOtp->handle($request->user());

        return back()->with('status', 'verification-code-sent');
    }
}
