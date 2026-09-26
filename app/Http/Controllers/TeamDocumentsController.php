<?php

namespace App\Http\Controllers;

use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

class TeamDocumentsController extends Controller
{
    public function __invoke(Team $team): RedirectResponse
    {
        Gate::authorize('view', $team);
        abort_unless($team->hasDocumentsFolder(), 404);

        return redirect()->away($team->documents_drive_url)->withHeaders(['Cache-Control' => 'private, no-store']);
    }
}
