<?php

namespace App\Support\Dashboard;

use Carbon\CarbonImmutable;

final class DashboardOverviewState
{
    private const ACTION_PRIORITY = [
        'registration_revision' => 1,
        'payment_required' => 2,
        'registration_draft' => 3,
        'submission_open' => 4,
    ];

    /**
     * @return array<string, string>
     */
    public static function scenarios(): array
    {
        return [
            'first_time_user' => 'First-time user',
            'active_participant' => 'Active participant',
            'revision_required' => 'Revision required',
            'payment_required' => 'Payment required',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function for(string $scenario, CarbonImmutable $now): array
    {
        $scenario = array_key_exists($scenario, self::scenarios()) ? $scenario : 'first_time_user';

        $state = [
            'scenario' => $scenario,
            'user' => [
                'name' => 'Alya Pratama',
                'email' => 'alya@example.test',
            ],
            'registrations' => [],
            'actions' => [],
            'summit_pass' => null,
            'summit_sales' => 'open',
            'timeline' => [
                [
                    'name' => 'Registration Open',
                    'competition' => 'MCC',
                    'starts_at' => '2026-08-28 09:00:00',
                ],
                [
                    'name' => 'Registration Close',
                    'competition' => 'BCC',
                    'starts_at' => '2026-09-25 23:59:00',
                ],
                [
                    'name' => 'Registration Close',
                    'competition' => 'BPC',
                    'starts_at' => '2026-09-30 23:59:00',
                ],
                [
                    'name' => 'Stage Submission',
                    'competition' => 'MCC',
                    'starts_at' => '2026-10-10 23:59:00',
                ],
                [
                    'name' => 'Qualification',
                    'competition' => 'BCC',
                    'starts_at' => '2026-10-17 09:00:00',
                ],
                [
                    'name' => 'Final',
                    'competition' => 'BPC',
                    'starts_at' => '2026-11-14 09:00:00',
                ],
                [
                    'name' => 'Main Event',
                    'competition' => 'Catalyst 2026',
                    'starts_at' => '2026-11-22 09:00:00',
                ],
            ],
        ];

        $state = match ($scenario) {
            'active_participant' => array_replace($state, [
                'registrations' => [
                    [
                        'code' => 'MCC',
                        'name' => 'Mini Case Competition',
                        'status' => 'APPROVED',
                        'status_label' => 'Registered',
                        'next_stage' => 'Stage Submission',
                        'deadline_at' => '2026-10-10 23:59:00',
                        'cta' => 'View registration',
                    ],
                    [
                        'code' => 'BCC',
                        'name' => 'Business Case Competition',
                        'status' => 'SUBMITTED',
                        'status_label' => 'Under verification',
                        'next_stage' => 'Registration verification',
                        'deadline_at' => '2026-09-25 23:59:00',
                        'cta' => 'View registration',
                    ],
                    [
                        'code' => 'BPC',
                        'name' => 'Business Plan Competition',
                        'status' => 'DRAFT',
                        'status_label' => 'Draft',
                        'next_stage' => 'Complete registration',
                        'deadline_at' => '2026-09-30 23:59:00',
                        'cta' => 'Continue registration',
                    ],
                ],
                'actions' => [
                    [
                        'type' => 'registration_draft',
                        'competition' => 'BPC',
                        'deadline_at' => '2026-09-30 23:59:00',
                    ],
                    [
                        'type' => 'submission_open',
                        'competition' => 'MCC',
                        'deadline_at' => '2026-10-10 23:59:00',
                    ],
                ],
                'summit_pass' => [
                    'name' => 'Summit Pass',
                    'status' => 'Active',
                    'date' => '22 November 2026',
                    'cta' => 'View pass',
                ],
            ]),
            'revision_required' => array_replace($state, [
                'registrations' => [
                    [
                        'code' => 'MCC',
                        'name' => 'Mini Case Competition',
                        'status' => 'REVISION_REQUESTED',
                        'status_label' => 'Revision requested',
                        'next_stage' => 'Update registration',
                        'deadline_at' => '2026-09-24 23:59:00',
                        'cta' => 'Review registration',
                    ],
                ],
                'actions' => [
                    [
                        'type' => 'registration_revision',
                        'competition' => 'MCC',
                        'deadline_at' => '2026-09-24 23:59:00',
                    ],
                ],
            ]),
            'payment_required' => array_replace($state, [
                'registrations' => [
                    [
                        'code' => 'BCC',
                        'name' => 'Business Case Competition',
                        'status' => 'PAYMENT_PENDING',
                        'status_label' => 'Payment required',
                        'next_stage' => 'Complete registration payment',
                        'deadline_at' => '2026-09-23 23:59:00',
                        'cta' => 'Continue payment',
                    ],
                ],
                'actions' => [
                    [
                        'type' => 'payment_required',
                        'payment_context' => 'competition',
                        'competition' => 'BCC',
                        'deadline_at' => '2026-09-23 23:59:00',
                    ],
                    [
                        'type' => 'payment_required',
                        'payment_context' => 'summit_pass',
                        'deadline_at' => '2026-10-04 23:59:00',
                    ],
                ],
            ]),
            default => $state,
        };

        $state['registrations'] = $this->normaliseRegistrations($state['registrations']);
        $state['summary'] = [
            'registered_competitions' => count($state['registrations']),
            'verification_pending' => count(array_filter(
                $state['registrations'],
                fn (array $registration): bool => in_array($registration['status'], ['SUBMITTED', 'UNDER_REVIEW'], true),
            )),
        ];
        $state['actions'] = $this->normaliseActions($state['actions']);
        $state['upcoming_event'] = $this->upcomingEvent($state['timeline'], $now);

        return $state;
    }

    /**
     * @param  array<int, array<string, string>>  $registrations
     * @return array<int, array<string, string>>
     */
    private function normaliseRegistrations(array $registrations): array
    {
        return array_map(function (array $registration): array {
            $deadline = CarbonImmutable::parse($registration['deadline_at'], 'Asia/Jakarta');

            return [
                ...$registration,
                'deadline_label' => $deadline->format('d M Y'),
            ];
        }, $registrations);
    }

    /**
     * @param  array<int, array<string, mixed>>  $actions
     * @return array<int, array<string, mixed>>
     */
    private function normaliseActions(array $actions): array
    {
        $actions = array_map(function (array $action): array {
            $competition = $action['competition'] ?? null;

            [$title, $description, $cta, $accent] = match ($action['type']) {
                'registration_revision' => [
                    "Update your {$competition} registration",
                    'Catalyst requested a few changes before your registration can be approved.',
                    'Update Registration',
                    'var(--color-status-warning)',
                ],
                'payment_required' => ($action['payment_context'] ?? 'competition') === 'summit_pass'
                    ? [
                        'Complete your Summit Pass payment',
                        'Your Summit Pass order is not complete yet.',
                        'Continue Payment',
                        'var(--color-status-warning)',
                    ]
                    : [
                        "Complete your {$competition} registration payment",
                        'Your competition registration payment is not complete yet.',
                        'Continue Payment',
                        'var(--color-status-warning)',
                    ],
                'submission_open' => [
                    "Submit your {$competition} Stage 1 entry",
                    'Submit your competition files before the deadline.',
                    'Go to Submission',
                    'var(--color-status-info)',
                ],
                default => [
                    "Complete your {$competition} registration",
                    'Your team registration is still in draft.',
                    'Continue Registration',
                    'var(--color-catalyst-primary)',
                ],
            };

            return [
                ...$action,
                'title' => $title,
                'description' => $description,
                'cta' => $cta,
                'accent' => $accent,
            ];
        }, $actions);

        usort($actions, function (array $left, array $right): int {
            $priority = (self::ACTION_PRIORITY[$left['type']] ?? PHP_INT_MAX) <=> (self::ACTION_PRIORITY[$right['type']] ?? PHP_INT_MAX);

            if ($priority !== 0) {
                return $priority;
            }

            return ($left['deadline_at'] ?? '9999-12-31 23:59:59') <=> ($right['deadline_at'] ?? '9999-12-31 23:59:59');
        });

        return $actions;
    }

    /**
     * @param  array<int, array<string, string>>  $timeline
     * @return array<string, string>|null
     */
    private function upcomingEvent(array $timeline, CarbonImmutable $now): ?array
    {
        $futureMilestones = array_filter($timeline, function (array $milestone) use ($now): bool {
            return CarbonImmutable::parse($milestone['starts_at'], 'Asia/Jakarta')->isAfter($now);
        });

        usort($futureMilestones, fn (array $left, array $right): int => $left['starts_at'] <=> $right['starts_at']);

        $milestone = $futureMilestones[0] ?? null;

        if ($milestone === null) {
            return null;
        }

        $startsAt = CarbonImmutable::parse($milestone['starts_at'], 'Asia/Jakarta');

        return [
            ...$milestone,
            'date_label' => $startsAt->format('d M Y'),
            'time_label' => $startsAt->format('H:i').' WIB',
            'time_remaining' => $this->timeRemaining($startsAt, $now),
        ];
    }

    private function timeRemaining(CarbonImmutable $startsAt, CarbonImmutable $now): string
    {
        $seconds = max(0, $startsAt->getTimestamp() - $now->getTimestamp());
        $days = intdiv($seconds, 86400);
        $hours = intdiv($seconds % 86400, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        if ($days > 0) {
            return "{$days}d {$hours}h";
        }

        if ($hours > 0) {
            return "{$hours}h {$minutes}m";
        }

        return "{$minutes}m";
    }
}
