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

class AddTeamMember
{
    /** @param array<string, mixed> $input */
    public function handle(User $actor, Team $team, array $input): TeamMember
    {
        Gate::forUser($actor)->authorize('manageMembers', $team);

        $input['email'] = Str::lower(trim((string) ($input['email'] ?? '')));
        $validated = Validator::make($input, $this->rules())->validate();

        return DB::transaction(function () use ($actor, $team, $validated): TeamMember {
            $team = Team::query()->lockForUpdate()->findOrFail($team->id);

            if (! $actor->isAdmin() && $team->isLocked()) {
                throw ValidationException::withMessages([
                    'team' => 'Team members can no longer be changed after payment proof is submitted.',
                ]);
            }

            if ($team->members()->count() >= 2) {
                throw ValidationException::withMessages([
                    'members' => 'A team may contain at most three people including the captain.',
                ]);
            }

            return $team->members()->create([
                'name' => trim($validated['name']),
                'email' => $validated['email'],
                'whatsapp' => trim($validated['whatsapp']),
                'ktm_url' => $validated['ktm_url'],
                'ktm_file_id' => trim($validated['ktm_file_id']),
            ]);
        });
    }

    /** @return array<string, array<int, mixed>> */
    private function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'string', 'email', 'max:255', new AvailableTeamEmail],
            'whatsapp' => ['required', 'string', 'max:20'],
            'ktm_url' => ['required', 'url:http,https'],
            'ktm_file_id' => ['required', 'string', 'max:255'],
        ];
    }
}
