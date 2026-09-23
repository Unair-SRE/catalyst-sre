<?php

namespace App\Http\Controllers;

use App\Contracts\PrivateFileUrlGenerator;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CaptainKtmController extends Controller
{
    public function __invoke(
        Request $request,
        User $user,
        PrivateFileUrlGenerator $urlGenerator,
    ): RedirectResponse {
        abort_unless($request->user()->isAdmin() || $request->user()->is($user), 403);
        abort_unless($user->hasCompleteKtm(), 404);

        return redirect()->away($urlGenerator->temporaryUrl($user->ktm_url));
    }
}
