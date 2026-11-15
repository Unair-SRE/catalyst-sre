<?php

namespace App\Actions\Submissions;

use App\Enums\RegistrationStatus;
use App\Models\CompetitionRound;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

class RequestSubmissionUpload
{
    public const MAX_BYTES = 5 * 1024 * 1024;

    public const ALLOWED_FORMATS = ['pdf', 'ppt', 'pptx'];

    /** @return array{cloud_name: string, upload_preset: string, folder: string, max_bytes: int, allowed_formats: array<int, string>} */
    public function handle(User $captain, Registration $registration, CompetitionRound $round): array
    {
        $submission = new Submission(['registration_id' => $registration->id]);
        $submission->setRelation('registration', $registration);
        Gate::forUser($captain)->authorize('submit', $submission);

        $this->guardEligible($registration, $round);

        $preset = config('services.cloudinary.upload_preset');

        if (! is_string($preset) || $preset === '') {
            throw ValidationException::withMessages([
                'submission' => 'Submission uploads are not configured yet.',
            ]);
        }

        return [
            'cloud_name' => (string) config('services.cloudinary.cloud_name'),
            'upload_preset' => $preset,
            'folder' => $this->folder($registration, $round),
            'max_bytes' => self::MAX_BYTES,
            'allowed_formats' => self::ALLOWED_FORMATS,
        ];
    }

    public static function folder(Registration $registration, CompetitionRound $round): string
    {
        return "catalyst/submissions/{$registration->id}/{$round->id}";
    }

    private function guardEligible(Registration $registration, CompetitionRound $round): void
    {
        if ($registration->status !== RegistrationStatus::Verified) {
            throw ValidationException::withMessages([
                'registration' => 'Only verified registrations can submit entries.',
            ]);
        }

        if ($round->competition_id !== $registration->competition_id) {
            throw ValidationException::withMessages([
                'round' => 'The stage belongs to a different competition.',
            ]);
        }

        if ($round->starts_at !== null && now()->isBefore($round->starts_at)) {
            throw ValidationException::withMessages([
                'round' => 'This submission stage has not opened yet.',
            ]);
        }

        if ($round->ends_at !== null && now()->isAfter($round->ends_at)) {
            throw ValidationException::withMessages([
                'round' => 'The submission deadline has passed.',
            ]);
        }
    }
}
