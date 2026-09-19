<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateTeam
{
    /** @param array<string, mixed> $input */
    public function handle(User $actor, Team $team, array $input): Team
    {
        Gate::forUser($actor)->authorize('update', $team);

        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:120'],
            'institution' => ['required', 'string', 'max:160'],
        ])->validate();

        return DB::transaction(function () use ($actor, $team, $validated): Team {
            $team = Team::query()->lockForUpdate()->findOrFail($team->id);

            if (! $actor->isAdmin() && $team->isLocked()) {
                throw ValidationException::withMessages([
                    'team' => 'The team can no longer be changed after payment proof is submitted.',
                ]);
            }

            $team->update([
                'name' => trim($validated['name']),
                'institution' => trim($validated['institution']),
            ]);

            return $team;
        });
    }
}
