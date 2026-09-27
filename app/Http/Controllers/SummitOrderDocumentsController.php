<?php

namespace App\Http\Controllers;

use App\Models\SummitOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class SummitOrderDocumentsController
{
    public function __invoke(SummitOrder $order): RedirectResponse
    {
        Gate::authorize('view', $order);
        abort_unless($order->hasPaymentFolder(), 404);

        return redirect()->away($order->payment_drive_url)
            ->withHeaders(['Cache-Control' => 'private, no-store']);
    }
}
