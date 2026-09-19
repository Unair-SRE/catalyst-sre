<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Rules\AvailableTeamEmail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class UpdateTeamMember
{
    /** @param array<string, mixed> $input */
    public function handle(User $actor, TeamMember $member, array $input): TeamMember
    {
        Gate::forUser($actor)->authorize('update', $member);

        $input['email'] = Str::lower(trim((string) ($input['email'] ?? '')));
        $validated = Validator::make($input, [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', new AvailableTeamEmail($member->id)],
            'whatsapp' => ['required', 'string', 'max:20'],
            'ktm_url' => ['required', 'url:http,https'],
            'ktm_file_id' => ['required', 'string', 'max:255'],
        ])->validate();

        return DB::transaction(function () use ($actor, $member, $validated): TeamMember {
            $team = Team::query()->lockForUpdate()->findOrFail($member->team_id);

            if (! $actor->isAdmin() && $team->isLocked()) {
                throw ValidationException::withMessages([
                    'team' => 'Team members can no longer be changed after payment proof is submitted.',
                ]);
            }

            $member->update([
                'name' => trim($validated['name']),
                'email' => $validated['email'],
                'whatsapp' => trim($validated['whatsapp']),
                'ktm_url' => $validated['ktm_url'],
                'ktm_file_id' => trim($validated['ktm_file_id']),
            ]);

            return $member;
        });
    }
}
