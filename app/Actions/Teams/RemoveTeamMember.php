<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class RemoveTeamMember
{
    public function handle(User $actor, TeamMember $member): void
    {
        Gate::forUser($actor)->authorize('delete', $member);

        DB::transaction(function () use ($actor, $member): void {
            $team = Team::query()->lockForUpdate()->findOrFail($member->team_id);

            if (! $actor->isAdmin() && $team->isLocked()) {
                throw ValidationException::withMessages([
                    'team' => 'Team members can no longer be changed after payment proof is submitted.',
                ]);
            }

            $member->delete();
        });
    }
}
