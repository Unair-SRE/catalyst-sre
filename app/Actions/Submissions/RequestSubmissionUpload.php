<?php

namespace App\Actions\Submissions;

use App\Enums\RegistrationStatus;
use App\Models\CompetitionRound;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\User;
use Cloudinary\Api\ApiUtils;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class RequestSubmissionUpload
{
    public const MAX_BYTES = 5 * 1024 * 1024;

    public const ALLOWED_FORMATS = ['pdf', 'ppt', 'pptx'];

    /**
     * @return array{cloud_name: string, resource_type: string, params: array<string, string>, max_bytes: int, allowed_formats: array<int, string>}
     */
    public function handle(User $captain, Registration $registration, CompetitionRound $round, string $extension): array
    {
        $submission = new Submission(['registration_id' => $registration->id]);
        $submission->setRelation('registration', $registration);
        Gate::forUser($captain)->authorize('submit', $submission);

        $this->guardEligible($registration, $round);

        $extension = strtolower(trim($extension));

        if (! in_array($extension, self::ALLOWED_FORMATS, true)) {
            throw ValidationException::withMessages([
                'file' => 'Only PDF, PPT, or PPTX files are accepted.',
            ]);
        }

        $resourceType = $extension === 'pdf' ? 'image' : 'raw';

        // The server owns the public id, so the signature locks the upload destination.
        $publicId = self::folder($registration, $round).'/'.Str::random(24);
        $publicId = $resourceType === 'raw' ? $publicId.'.'.$extension : $publicId;

        $params = [
            'public_id' => $publicId,
            'timestamp' => (string) now()->getTimestamp(),
            'type' => 'authenticated',
            'allowed_formats' => implode(',', self::ALLOWED_FORMATS),
        ];

        return [
            'cloud_name' => $this->configValue('cloud_name'),
            'resource_type' => $resourceType,
            'params' => [
                ...$params,
                'api_key' => $this->configValue('api_key'),
                'signature' => ApiUtils::signParameters($params, $this->configValue('api_secret')),
            ],
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

        match ($round->windowState()) {
            'unscheduled' => throw ValidationException::withMessages([
                'round' => 'This submission stage has not been scheduled yet.',
            ]),
            'upcoming' => throw ValidationException::withMessages([
                'round' => 'This submission stage has not opened yet.',
            ]),
            'closed' => throw ValidationException::withMessages([
                'round' => 'The submission deadline has passed.',
            ]),
            default => null,
        };
    }

    private function configValue(string $key): string
    {
        $value = config('services.cloudinary.'.$key);

        if (! is_string($value) || $value === '') {
            throw new RuntimeException('CLOUDINARY_'.strtoupper($key).' is not configured.');
        }

        return $value;
    }
}
