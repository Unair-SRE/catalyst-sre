<?php

namespace App\Http\Controllers;

use App\Contracts\CompetitionPaymentStorage;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompetitionPaymentProofController extends Controller
{
    public function __invoke(
        Request $request,
        Payment $payment,
        CompetitionPaymentStorage $urlGenerator,
    ): RedirectResponse {
        Gate::authorize('view', $payment);

        abort_unless($payment->hasProof(), 404);

        return redirect()->away($urlGenerator->temporaryUrl($payment->payment_proof_url));
    }
}
