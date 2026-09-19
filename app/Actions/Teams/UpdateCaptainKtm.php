<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateCaptainKtm
{
    /** @param array<string, mixed> $input */
    public function handle(User $actor, Team $team, array $input): User
    {
        Gate::forUser($actor)->authorize('update', $team);

        $validated = Validator::make($input, [
            'ktm_url' => ['required', 'url:http,https'],
            'ktm_file_id' => ['required', 'string', 'max:255'],
        ])->validate();

        return DB::transaction(function () use ($actor, $team, $validated): User {
            $team = Team::query()->lockForUpdate()->findOrFail($team->id);

            if (! $actor->isAdmin() && $team->isLocked()) {
                throw ValidationException::withMessages([
                    'ktm_url' => 'The KTM can no longer be changed after payment proof is submitted.',
                ]);
            }

            $captain = $team->captain()->firstOrFail();
            $captain->update($validated);

            return $captain;
        });
    }
}
