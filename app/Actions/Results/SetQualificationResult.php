<?php

namespace App\Actions\Results;

use App\Enums\QualificationStatus;
use App\Enums\RegistrationStatus;
use App\Models\CompetitionRound;
use App\Models\QualificationResult;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SetQualificationResult
{
    public function handle(
        User $admin,
        Registration $registration,
        CompetitionRound $round,
        QualificationStatus $status,
    ): QualificationResult {
        Gate::forUser($admin)->authorize('create', QualificationResult::class);

        if ($registration->status !== RegistrationStatus::Verified) {
            throw ValidationException::withMessages([
                'registration' => 'Results can only be recorded for verified registrations.',
            ]);
        }

        if ($round->competition_id !== $registration->competition_id) {
            throw ValidationException::withMessages([
                'round' => 'The round belongs to a different competition.',
            ]);
        }

        return DB::transaction(function () use ($admin, $registration, $round, $status): QualificationResult {
            $lockedRegistration = Registration::query()->lockForUpdate()->findOrFail($registration->id);
            $lockedRound = CompetitionRound::query()->lockForUpdate()->findOrFail($round->id);

            $previousRound = CompetitionRound::query()
                ->where('competition_id', $lockedRound->competition_id)
                ->where('round_order', '<', $lockedRound->round_order)
                ->orderByDesc('round_order')
                ->first();

            if ($previousRound !== null) {
                $previousResult = QualificationResult::query()
                    ->where('registration_id', $lockedRegistration->id)
                    ->where('competition_round_id', $previousRound->id)
                    ->first();

                if ($previousResult === null || $previousResult->status !== QualificationStatus::Qualified) {
                    throw ValidationException::withMessages([
                        'registration' => 'The team did not qualify in the previous round.',
                    ]);
                }
            }

            $result = QualificationResult::query()->updateOrCreate(
                [
                    'registration_id' => $lockedRegistration->id,
                    'competition_round_id' => $lockedRound->id,
                ],
                [
                    'status' => $status,
                    'decided_by' => $admin->id,
                    'decided_at' => now(),
                ],
            );

            Log::info('Qualification result recorded.', [
                'operation' => 'qualification_result.set',
                'registration_id' => $lockedRegistration->id,
                'round_id' => $lockedRound->id,
                'status' => $status->value,
                'admin_id' => $admin->id,
            ]);

            return $result->fresh(['registration', 'round']);
        });
    }
}
