<?php

namespace App\Http\Controllers;

use App\Contracts\PrivateFileUrlGenerator;
use App\Models\TeamMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class TeamMemberKtmController extends Controller
{
    public function __invoke(
        TeamMember $teamMember,
        PrivateFileUrlGenerator $urlGenerator,
    ): RedirectResponse {
        Gate::authorize('view', $teamMember);
        abort_unless($teamMember->hasCompleteKtm(), 404);

        return redirect()->away($urlGenerator->temporaryUrl($teamMember->ktm_url));
    }
}
