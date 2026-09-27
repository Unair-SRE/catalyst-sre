<?php

namespace App\Actions\Teams;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Models\Team;
use App\Models\User;
use App\Rules\GoogleDriveFolder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateDriveFolder
{
    public function handle(User $actor, Team $team, string $url, bool $accessConfirmed): Team
    {
        abort_unless($actor->isAdmin() || $team->captain_id === $actor->id, 403);
        Validator::make([
            'documents_drive_url' => $url,
            'documents_access_confirmed' => $accessConfirmed,
        ], [
            'documents_drive_url' => ['required', 'string', 'max:2048', new GoogleDriveFolder],
            'documents_access_confirmed' => ['accepted'],
        ])->validate();

        return DB::transaction(function () use ($actor, $team, $url): Team {
            $team = Team::query()->lockForUpdate()->findOrFail($team->id);
            if ($team->documents_drive_url === $url) {
                return $team;
            }
            // Existing locked teams may supply their first folder during migration.
            if (! $actor->isAdmin() && $team->isLocked() && filled($team->documents_drive_url)) {
                throw ValidationException::withMessages(['documents_drive_url' => 'Contact the committee to replace a folder after payment submission. You can still correct files inside the same folder.']);
            }
            $team->update(['documents_drive_url' => $url]);
            foreach ($team->registrations()->with('payment')->get() as $registration) {
                $payment = $registration->payment;
                if ($payment?->documents_submitted_at) {
                    $payment->update([
                        'status' => PaymentStatus::WaitingVerification,
                        'verified_by' => null,
                        'verified_at' => null,
                        'review_note' => 'The team folder changed. All documents require a new review.',
                    ]);
                    $registration->update(['status' => RegistrationStatus::Pending]);
                }
            }
            Log::info('Team document folder changed.', ['team_id' => $team->id, 'actor_id' => $actor->id]);

            return $team;
        });
    }
}
