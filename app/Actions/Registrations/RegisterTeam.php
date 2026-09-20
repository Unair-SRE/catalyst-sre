<?php

namespace App\Actions\Registrations;

use App\Enums\RegistrationStatus;
use App\Models\Competition;
use App\Models\Registration;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class RegisterTeam
{
    public function handle(User $captain, Team $team, Competition $competition): Registration
    {
        Gate::forUser($captain)->authorize('create', Registration::class);

        if ($team->captain_id !== $captain->id) {
            throw ValidationException::withMessages([
                'team' => 'Only the team captain may register this team.',
            ]);
        }

        return DB::transaction(function () use ($team, $competition): Registration {
            $lockedTeam = Team::query()
                ->with(['captain', 'members', 'registrations.competition'])
                ->lockForUpdate()
                ->findOrFail($team->id);

            $this->validate($lockedTeam, $competition);

            return Registration::query()->create([
                'team_id' => $lockedTeam->id,
                'competition_id' => $competition->id,
                'status' => RegistrationStatus::Pending,
            ]);
        });
    }

    private function validate(Team $team, Competition $competition): void
    {
        if (! $competition->acceptsRegistration()) {
            throw ValidationException::withMessages([
                'competition' => 'Registration for this competition is closed.',
            ]);
        }

        if ($team->peopleCount() < 1 || $team->peopleCount() > 3) {
            throw ValidationException::withMessages([
                'team' => 'A team must contain between one and three people.',
            ]);
        }

        if (! $team->hasCompleteKtm()) {
            throw ValidationException::withMessages([
                'team' => 'The captain and every team member must have a KTM before registration.',
            ]);
        }

        if ($team->registrations->contains('competition_id', $competition->id)) {
            throw ValidationException::withMessages([
                'competition' => 'This team is already registered for the selected competition.',
            ]);
        }

        if ($competition->code->isMainCompetition()
            && $team->registrations->contains(
                fn (Registration $registration): bool => $registration->competition->code->isMainCompetition(),
            )) {
            throw ValidationException::withMessages([
                'competition' => 'A team may register for only one main competition.',
            ]);
        }
    }
}
