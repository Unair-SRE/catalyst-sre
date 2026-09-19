<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;

class CreateTeam
{
    /** @param array<string, mixed> $input */
    public function handle(User $captain, array $input): Team
    {
        Gate::forUser($captain)->authorize('create', Team::class);

        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:120'],
            'institution' => ['required', 'string', 'max:160'],
        ])->validate();

        return Team::create([
            'captain_id' => $captain->id,
            'name' => trim($validated['name']),
            'institution' => trim($validated['institution']),
        ]);
    }
}
