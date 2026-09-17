<?php

namespace App\Support\Dashboard;

use Carbon\CarbonImmutable;

final class DashboardSubmissionState
{
    /**
     * @return array<string, string>
     */
    public static function indexScenarios(): array
    {
        return [
            'no_registration' => 'No registration',
            'waiting_verification' => 'Waiting verification',
            'mixed_submission' => 'Mixed submission',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function detailScenarios(): array
    {
        return [
            'upcoming' => 'Upcoming',
            'open_empty' => 'Open — no files',
            'uploading' => 'Uploading',
            'upload_failed' => 'Upload failed',
            'submitted' => 'Submitted',
            'editable_after_unsubmit' => 'Editable after unsubmit',
            'late' => 'Late submission',
            'closed_no_submission' => 'Closed — no submission',
            'closed_submitted' => 'Closed — submitted',
            'revision_required' => 'Revision required',
            'accepted' => 'Accepted',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function index(string $scenario): array
    {
        $scenario = array_key_exists($scenario, self::indexScenarios()) ? $scenario : 'no_registration';

        $participations = match ($scenario) {
            'waiting_verification' => [
                ['competition' => 'bcc', 'registration_status' => 'UNDER_REVIEW', 'stage' => 'stage-1', 'stage_state' => 'upcoming', 'submission_state' => 'not_submitted', 'access' => 'locked'],
            ],
            'mixed_submission' => [
                ['competition' => 'mcc', 'registration_status' => 'VERIFIED', 'stage' => 'stage-1', 'stage_state' => 'open', 'submission_state' => 'not_submitted', 'access' => 'available'],
                ['competition' => 'bcc', 'registration_status' => 'UNDER_REVIEW', 'stage' => 'stage-1', 'stage_state' => 'upcoming', 'submission_state' => 'not_submitted', 'access' => 'locked'],
                ['competition' => 'bpc', 'registration_status' => 'VERIFIED', 'stage' => 'stage-1', 'stage_state' => 'open', 'submission_state' => 'submitted', 'access' => 'available', 'submitted_at' => '2026-11-07 21:42:00'],
            ],
            default => [],
        };

        return array_map(fn (array $participation): array => $this->normaliseIndexParticipation($participation), $participations);
    }

    /**
     * @return array<string, mixed>|null
     */
    public function detail(string $competitionSlug, ?string $stageSlug, string $scenario): ?array
    {
        $competition = $this->competitions()[$competitionSlug] ?? null;

        if ($competition === null) {
            return null;
        }

        $stages = array_values(array_filter($competition['stages'], fn (array $stage): bool => $stage['eligible']));
        $stage = collect($stages)->firstWhere('slug', $stageSlug ?? $stages[0]['slug'] ?? null);

        if ($stage === null) {
            return null;
        }

        $scenario = array_key_exists($scenario, self::detailScenarios()) ? $scenario : 'open_empty';
        $state = $this->scenarioState($scenario, $stage);

        return [
            'scenario' => $scenario,
            'competition' => $competition,
            'stages' => array_map(fn (array $item): array => [
                'slug' => $item['slug'],
                'name' => $item['name'],
            ], $stages),
            'stage' => [
                ...$stage,
                'open_label' => $this->dateTimeLabel($stage['open_at']),
                'deadline_label' => $this->dateTimeLabel($stage['deadline_at']),
                'late_close_label' => $stage['late_close_at'] ? $this->dateTimeLabel($stage['late_close_at']) : null,
            ],
            'requirements' => [
                'allowed_extensions' => ['pdf', 'ppt', 'pptx'],
                'allowed_label' => 'PDF, PPT, PPTX',
                'max_file_bytes' => 5 * 1024 * 1024,
                'max_file_label' => '5 MB per file',
                'max_files' => 3,
            ],
            'registration' => $this->registrationStatus($state['registration_status']),
            'stage_state' => $state['stage_state'],
            'stage_lifecycle' => $this->stageLifecycle($state['stage_state']),
            'submission_state' => $state['submission_state'],
            'submission' => $this->submissionStatus($state['submission_state']),
            'submission_access' => $state['access'],
            'access' => $this->accessStatus($state['access']),
            'files' => $state['files'],
            'history' => $state['history'],
            'active_submission_at' => $state['active_submission_at'],
            'active_submission_label' => $state['active_submission_at'] ? $this->dateTimeLabel($state['active_submission_at']) : null,
            'lateness' => $state['active_submission_at'] ? $this->lateness($state['active_submission_at'], $stage['deadline_at']) : null,
            'revision_note' => $state['revision_note'],
            'contact_url' => 'https://example.com/catalyst-contact',
        ];
    }

    /**
     * @return array<string, string>
     */
    public function stageLifecycle(string $state): array
    {
        return match ($state) {
            'upcoming' => ['label' => 'Upcoming', 'tone' => 'neutral'],
            'open' => ['label' => 'Open', 'tone' => 'success'],
            'late_window' => ['label' => 'Late window', 'tone' => 'warning'],
            default => ['label' => 'Closed', 'tone' => 'error'],
        };
    }

    /**
     * @return array<string, string>
     */
    public function submissionStatus(string $state): array
    {
        return match ($state) {
            'submitted' => ['label' => 'Submitted', 'tone' => 'success'],
            'editable' => ['label' => 'Editable', 'tone' => 'info'],
            'late' => ['label' => 'Submitted late', 'tone' => 'warning'],
            'revision_required' => ['label' => 'Revision required', 'tone' => 'warning'],
            'accepted' => ['label' => 'Accepted', 'tone' => 'success'],
            default => ['label' => 'Not submitted', 'tone' => 'neutral'],
        };
    }

    /**
     * @return array<string, string>
     */
    public function accessStatus(string $access): array
    {
        return match ($access) {
            'available' => ['label' => 'Available', 'tone' => 'success'],
            'closed' => ['label' => 'Closed', 'tone' => 'error'],
            default => ['label' => 'Locked', 'tone' => 'neutral'],
        };
    }

    /**
     * @param  array<string, mixed>  $participation
     * @return array<string, mixed>
     */
    private function normaliseIndexParticipation(array $participation): array
    {
        $competition = $this->competitions()[$participation['competition']];
        $stage = collect($competition['stages'])->firstWhere('slug', $participation['stage']);

        $href = $participation['registration_status'] !== 'VERIFIED'
            ? route('dashboard.registration.show', ['competition' => $competition['slug']])
            : route('dashboard.submission.show', ['competition' => $competition['slug'], 'stage' => $stage['slug']]);

        return [
            'competition' => $competition,
            'stage' => [
                ...$stage,
                'open_label' => $this->dateTimeLabel($stage['open_at']),
                'deadline_label' => $this->dateTimeLabel($stage['deadline_at']),
            ],
            'registration' => $this->registrationStatus($participation['registration_status']),
            'registration_status' => $participation['registration_status'],
            'stage_state' => $participation['stage_state'],
            'stage_lifecycle' => $this->stageLifecycle($participation['stage_state']),
            'submission_state' => $participation['submission_state'],
            'submission' => $this->submissionStatus($participation['submission_state']),
            'submission_access' => $participation['access'],
            'access' => $this->accessStatus($participation['access']),
            'submitted_at' => $participation['submitted_at'] ?? null,
            'submitted_label' => isset($participation['submitted_at']) ? $this->dateTimeLabel($participation['submitted_at']) : null,
            'href' => $href,
            'cta' => $participation['registration_status'] !== 'VERIFIED'
                ? 'Complete Registration'
                : 'View Submission',
        ];
    }

    /**
     * @param  array<string, mixed>  $stage
     * @return array<string, mixed>
     */
    private function scenarioState(string $scenario, array $stage): array
    {
        $files = [
            $this->file('strategy-deck.pdf', 'application/pdf', 2_480_000),
            $this->file('market-analysis.pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 1_820_000),
        ];
        $history = [
            $this->history('Uploaded', '2026-11-06 18:20:00', 'strategy-deck.pdf · Version 1'),
            $this->history('Submitted', '2026-11-07 21:42:00', '2 files · Version 1'),
        ];

        return match ($scenario) {
            'upcoming' => $this->state('VERIFIED', 'upcoming', 'not_submitted', 'available'),
            'uploading' => $this->state('VERIFIED', 'open', 'editable', 'available', [
                $this->file('strategy-deck.pdf', 'application/pdf', 2_480_000, 'uploading', 64),
            ]),
            'upload_failed' => $this->state('VERIFIED', 'open', 'editable', 'available', [
                $this->file('strategy-deck.pdf', 'application/pdf', 2_480_000, 'failed', 0, 'network_error'),
            ]),
            'submitted' => $this->state('VERIFIED', 'open', 'submitted', 'available', $files, $history, '2026-11-07 21:42:00'),
            'editable_after_unsubmit' => $this->state('VERIFIED', 'open', 'editable', 'available', $files, [
                ...$history,
                $this->history('Unsubmitted', '2026-11-07 22:05:00', 'Version 1 remains in history'),
                $this->history('File replaced', '2026-11-07 22:12:00', 'market-analysis.pptx · Version 2'),
            ]),
            'late' => $this->state('VERIFIED', 'late_window', 'late', 'available', $files, [
                ...$history,
                $this->history('Resubmitted late', '2026-11-08 00:07:00', '2 files · Version 2'),
            ], '2026-11-08 00:07:00'),
            'closed_no_submission' => $this->state('VERIFIED', 'closed', 'not_submitted', 'closed'),
            'closed_submitted' => $this->state('VERIFIED', 'closed', 'submitted', 'closed', $files, $history, '2026-11-07 21:42:00'),
            'revision_required' => $this->state('VERIFIED', 'open', 'revision_required', 'available', $files, $history, null, 'Please replace the presentation with the requested financial assumptions.'),
            'accepted' => $this->state('VERIFIED', 'closed', 'accepted', 'closed', $files, [
                ...$history,
                $this->history('Accepted', '2026-11-09 10:00:00', 'Catalyst confirmed this submission.'),
            ], '2026-11-07 21:42:00'),
            default => $this->state('VERIFIED', 'open', 'not_submitted', 'available'),
        };
    }

    /**
     * @param  array<int, array<string, mixed>>  $files
     * @param  array<int, array<string, string>>  $history
     * @return array<string, mixed>
     */
    private function state(string $registrationStatus, string $stageState, string $submissionState, string $access, array $files = [], array $history = [], ?string $activeSubmissionAt = null, ?string $revisionNote = null): array
    {
        return [
            'registration_status' => $registrationStatus,
            'stage_state' => $stageState,
            'submission_state' => $submissionState,
            'access' => $access,
            'files' => $files,
            'history' => $history,
            'active_submission_at' => $activeSubmissionAt,
            'revision_note' => $revisionNote,
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function competitions(): array
    {
        return [
            'mcc' => [
                'slug' => 'mcc',
                'name' => 'Mini Case Competition',
                'short_name' => 'MCC',
                'stages' => [
                    [
                        'slug' => 'stage-1',
                        'name' => 'Case Submission',
                        'eligible' => true,
                        'open_at' => '2026-10-17 09:00:00',
                        'deadline_at' => '2026-10-24 23:59:00',
                        'late_close_at' => '2026-10-25 12:00:00',
                    ],
                    [
                        'slug' => 'stage-2',
                        'name' => 'Final Submission',
                        'eligible' => true,
                        'open_at' => '2026-11-12 09:00:00',
                        'deadline_at' => '2026-11-14 23:59:00',
                        'late_close_at' => null,
                    ],
                ],
            ],
            'bcc' => [
                'slug' => 'bcc',
                'name' => 'Business Case Competition',
                'short_name' => 'BCC',
                'stages' => [[
                    'slug' => 'stage-1',
                    'name' => 'Stage 1 Submission',
                    'eligible' => true,
                    'open_at' => '2026-11-07 09:00:00',
                    'deadline_at' => '2026-11-07 23:59:00',
                    'late_close_at' => '2026-11-08 01:00:00',
                ]],
            ],
            'bpc' => [
                'slug' => 'bpc',
                'name' => 'Business Plan Competition',
                'short_name' => 'BPC',
                'stages' => [[
                    'slug' => 'stage-1',
                    'name' => 'Stage 1 Submission',
                    'eligible' => true,
                    'open_at' => '2026-11-01 09:00:00',
                    'deadline_at' => '2026-11-07 23:59:00',
                    'late_close_at' => '2026-11-08 01:00:00',
                ]],
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    private function registrationStatus(string $status): array
    {
        return match ($status) {
            'VERIFIED' => ['label' => 'Verified', 'tone' => 'success'],
            'UNDER_REVIEW' => ['label' => 'Under review', 'tone' => 'info'],
            default => ['label' => 'Not verified', 'tone' => 'neutral'],
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function file(string $name, string $type, int $sizeBytes, string $state = 'uploaded', int $progress = 100, ?string $error = null): array
    {
        return [
            'id' => str($name)->slug('-')->append('-demo')->value(),
            'name' => $name,
            'type' => $type,
            'size_bytes' => $sizeBytes,
            'size_label' => $this->fileSize($sizeBytes),
            'state' => $state,
            'progress' => $progress,
            'error' => $error,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function history(string $event, string $timestamp, string $detail): array
    {
        return [
            'event' => $event,
            'timestamp' => $timestamp,
            'timestamp_label' => $this->dateTimeLabel($timestamp),
            'detail' => $detail,
        ];
    }

    private function dateTimeLabel(string $value): string
    {
        return CarbonImmutable::parse($value, 'Asia/Jakarta')->format('d F Y · H:i').' WIB';
    }

    private function fileSize(int $bytes): string
    {
        return number_format($bytes / 1024 / 1024, 1).' MB';
    }

    private function lateness(string $submittedAt, string $deadlineAt): ?string
    {
        $seconds = CarbonImmutable::parse($submittedAt, 'Asia/Jakarta')->getTimestamp() - CarbonImmutable::parse($deadlineAt, 'Asia/Jakarta')->getTimestamp();

        if ($seconds <= 0) {
            return null;
        }

        $minutes = intdiv($seconds, 60);

        return $minutes < 60
            ? "{$minutes} minutes late"
            : intdiv($minutes, 60).'h '.($minutes % 60).'m late';
    }
}
