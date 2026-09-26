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

class SaveDriveTeamMember
{
    public function handle(User $actor, Team $team, array $input, ?int $memberId = null): TeamMember
    {
        Gate::forUser($actor)->authorize('manageMembers', $team);

        return DB::transaction(function () use ($actor, $team, $input, $memberId): TeamMember {
            $team = Team::query()->lockForUpdate()->findOrFail($team->id);
            Gate::forUser($actor)->authorize('manageMembers', $team);
            $member = $memberId === null ? null : $team->members()->findOrFail($memberId);
            $input['email'] = Str::lower(trim($input['email'] ?? ''));
            $data = Validator::make($input, [
                'name' => ['required', 'string', 'max:120'],
                'email' => ['required', 'email', 'max:255', new AvailableTeamEmail($member?->id)],
                'whatsapp' => ['required', 'string', 'max:20'],
            ])->validate();
            if ($member === null && $team->members()->count() >= 2) {
                throw ValidationException::withMessages(['members' => 'A team may contain at most three people including the captain.']);
            }
            $data['name'] = trim($data['name']);
            $data['whatsapp'] = trim($data['whatsapp']);
            if ($member) {
                $member->update($data);

                return $member->refresh();
            }

            return $team->members()->create($data);
        });
    }
}
