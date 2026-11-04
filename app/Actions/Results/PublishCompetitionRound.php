<?php

namespace App\Actions\Results;

use App\Enums\QualificationStatus;
use App\Models\CompetitionRound;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class PublishCompetitionRound
{
    /** @return array{0: CompetitionRound, 1: bool} */
    public function handle(User $admin, CompetitionRound $round): array
    {
        Gate::forUser($admin)->authorize('publish', $round);

        return DB::transaction(function () use ($admin, $round): array {
            $lockedRound = CompetitionRound::query()
                ->withCount(['results as qualified_count' => fn ($query): mixed => $query->where('status', QualificationStatus::Qualified)])
                ->lockForUpdate()
                ->findOrFail($round->id);

            if ($lockedRound->isPublished()) {
                return [$lockedRound, false];
            }

            $lockedRound->update([
                'published_at' => now(),
                'published_by' => $admin->id,
            ]);

            Log::info('Competition round published.', [
                'operation' => 'competition_round.publish',
                'round_id' => $lockedRound->id,
                'admin_id' => $admin->id,
            ]);

            return [$lockedRound->fresh(), true];
        });
    }
}
