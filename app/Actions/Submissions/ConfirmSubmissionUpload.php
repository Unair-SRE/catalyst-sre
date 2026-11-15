<?php

namespace App\Actions\Submissions;

use App\Contracts\SubmissionFileInspector;
use App\Enums\RegistrationStatus;
use App\Models\CompetitionRound;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ConfirmSubmissionUpload
{
    public function __construct(private readonly SubmissionFileInspector $inspector) {}

    public function handle(User $captain, Registration $registration, CompetitionRound $round, string $publicId): Submission
    {
        $submission = new Submission(['registration_id' => $registration->id]);
        $submission->setRelation('registration', $registration);
        Gate::forUser($captain)->authorize('submit', $submission);

        $expectedFolder = RequestSubmissionUpload::folder($registration, $round);
        $file = $this->inspector->inspect(trim($publicId));

        if ($file === null) {
            throw ValidationException::withMessages([
                'file' => 'The uploaded file could not be found. Upload again before confirming.',
            ]);
        }

        if ($file->folder !== $expectedFolder) {
            throw ValidationException::withMessages([
                'file' => 'The uploaded file does not belong to this submission.',
            ]);
        }

        if (! in_array($file->format, RequestSubmissionUpload::ALLOWED_FORMATS, true)) {
            throw ValidationException::withMessages([
                'file' => 'Only PDF, PPT, or PPTX files are accepted.',
            ]);
        }

        if ($file->bytes <= 0 || $file->bytes > RequestSubmissionUpload::MAX_BYTES) {
            throw ValidationException::withMessages([
                'file' => 'The file must not exceed 5 MB.',
            ]);
        }

        if (Submission::query()->where('public_id', $file->publicId)->exists()) {
            throw ValidationException::withMessages([
                'file' => 'This file has already been submitted.',
            ]);
        }

        return DB::transaction(function () use ($captain, $registration, $round, $file): Submission {
            $lockedRegistration = Registration::query()->lockForUpdate()->findOrFail($registration->id);
            $lockedRound = CompetitionRound::query()->lockForUpdate()->findOrFail($round->id);

            if ($lockedRegistration->status !== RegistrationStatus::Verified) {
                throw ValidationException::withMessages([
                    'registration' => 'Only verified registrations can submit entries.',
                ]);
            }

            if ($lockedRound->competition_id !== $lockedRegistration->competition_id) {
                throw ValidationException::withMessages([
                    'round' => 'The stage belongs to a different competition.',
                ]);
            }

            if (($lockedRound->starts_at !== null && now()->isBefore($lockedRound->starts_at))
                || ($lockedRound->ends_at !== null && now()->isAfter($lockedRound->ends_at))) {
                throw ValidationException::withMessages([
                    'round' => 'Submissions are closed for this stage.',
                ]);
            }

            $existing = Submission::query()
                ->where('registration_id', $lockedRegistration->id)
                ->where('competition_round_id', $lockedRound->id)
                ->lockForUpdate()
                ->first();

            $submission = Submission::query()->updateOrCreate(
                [
                    'registration_id' => $lockedRegistration->id,
                    'competition_round_id' => $lockedRound->id,
                ],
                [
                    'version' => $existing === null ? 1 : $existing->version + 1,
                    'public_id' => $file->publicId,
                    'file_url' => $file->url,
                    'bytes' => $file->bytes,
                    'format' => $file->format,
                    'etag' => $file->etag,
                    'submitted_by' => $captain->id,
                    'submitted_at' => now(),
                ],
            );

            Log::info('Submission confirmed.', [
                'operation' => 'submission.confirm',
                'registration_id' => $lockedRegistration->id,
                'round_id' => $lockedRound->id,
                'version' => $submission->version,
                'actor_id' => $captain->id,
            ]);

            return $submission->fresh(['registration', 'round']);
        });
    }
}
