<?php

namespace App\Actions\Teams;

use App\Models\Team;
use App\Models\User;
use App\Rules\AvailableTeamEmail;
use App\Rules\GoogleDriveFolder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreateDriveTeam
{
    public function handle(User $captain, array $input, array $members = []): Team
    {
        Gate::forUser($captain)->authorize('create', Team::class);
        $members = array_map(fn (array $member): array => [
            ...$member, 'email' => Str::lower(trim($member['email'] ?? '')),
        ], array_values($members));
        $validated = Validator::make(['team' => $input, 'members' => $members], [
            'team.name' => ['required', 'string', 'max:120'],
            'team.institution' => ['required', 'string', 'max:160'],
            'team.documents_drive_url' => ['required', 'string', 'max:2048', new GoogleDriveFolder],
            'team.documents_access_confirmed' => ['accepted'],
            'members' => ['array', 'max:2'],
            'members.*.name' => ['required', 'string', 'max:120'],
            'members.*.email' => ['required', 'email', 'max:255', 'distinct:ignore_case', new AvailableTeamEmail],
            'members.*.whatsapp' => ['required', 'string', 'max:20'],
        ])->validate();

        return DB::transaction(function () use ($captain, $validated): Team {
            $captain = User::query()->lockForUpdate()->findOrFail($captain->id);
            if ($captain->captainedTeam()->exists()) {
                throw ValidationException::withMessages(['team' => 'You already captain a team.']);
            }
            $team = $captain->captainedTeam()->create([
                'name' => trim($validated['team']['name']),
                'institution' => trim($validated['team']['institution']),
                'documents_drive_url' => $validated['team']['documents_drive_url'],
            ]);
            foreach ($validated['members'] as $member) {
                $team->members()->create([
                    'name' => trim($member['name']),
                    'email' => $member['email'],
                    'whatsapp' => trim($member['whatsapp']),
                ]);
            }

            return $team->load(['captain', 'members']);
        });
    }
}
