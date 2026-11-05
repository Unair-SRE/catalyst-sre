<?php

namespace App\Actions\Results;

use App\Enums\EmailStatus;
use App\Enums\QualificationStatus;
use App\Jobs\SendResultEmail;
use App\Models\CompetitionRound;
use App\Models\EmailNotification;
use App\Models\QualificationResult;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Throwable;

class PublishCompetitionRound
{
    /** @return array{0: CompetitionRound, 1: bool} */
    public function handle(User $admin, CompetitionRound $round): array
    {
        Gate::forUser($admin)->authorize('publish', $round);

        [$publishedRound, $didPublish] = DB::transaction(function () use ($admin, $round): array {
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

        if ($didPublish) {
            $this->dispatchResultEmails($publishedRound);
        }

        return [$publishedRound, $didPublish];
    }

    private function dispatchResultEmails(CompetitionRound $round): void
    {
        $results = QualificationResult::query()
            ->with(['registration.team.captain'])
            ->where('competition_round_id', $round->id)
            ->get();

        foreach ($results as $result) {
            $captain = $result->registration->team->captain ?? null;

            if ($captain === null) {
                continue;
            }

            try {
                $notification = EmailNotification::query()->create([
                    'user_id' => $captain->getKey(),
                    'recipient_email' => $captain->email,
                    'type' => 'RESULT_PUBLISHED',
                    'status' => EmailStatus::Pending,
                ]);

                SendResultEmail::dispatch($notification->id, $result->id);
            } catch (Throwable $exception) {
                report($exception);
            }
        }
    }
}
