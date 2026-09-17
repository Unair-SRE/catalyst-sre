<?php

namespace App\Support\Dashboard;

final class DashboardRegistrationState
{
    /**
     * @return array<string, string>
     */
    public static function indexScenarios(): array
    {
        return [
            'first_time_user' => 'First-time user',
            'mixed_registration' => 'Mixed registration',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function detailScenarios(): array
    {
        return [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'under_review' => 'Under review',
            'revision_required' => 'Revision required',
            'verified' => 'Verified',
            'rejected' => 'Rejected',
            'payment_rejected' => 'Payment rejected',
            'payment_waived' => 'Payment waived',
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function index(string $scenario): array
    {
        $scenario = array_key_exists($scenario, self::indexScenarios()) ? $scenario : 'first_time_user';

        return array_map(function (array $competition) use ($scenario): array {
            $registration = $scenario === 'mixed_registration' ? $this->mixedRegistration($competition['slug']) : null;

            return $this->normaliseIndexCompetition($competition, $registration);
        }, array_values($this->competitions()));
    }

    /**
     * @return array<string, mixed>|null
     */
    public function detail(string $slug, string $scenario): ?array
    {
        $competition = $this->competitions()[$slug] ?? null;

        if ($competition === null) {
            return null;
        }

        $scenario = array_key_exists($scenario, self::detailScenarios()) ? $scenario : 'draft';
        $profile = [
            'legal_name' => 'Alya Pratama',
            'email' => 'alya@example.test',
            'whatsapp' => '+62 812 0000 0000',
            'student_id' => '2026123456',
        ];
        $form = [
            'team_name' => 'Catalyst Collective',
            'institution' => 'Universitas Indonesia',
            'eligible' => false,
            'members' => [
                [
                    'role' => 'Captain',
                    ...$profile,
                ],
                [
                    'role' => 'Member 2',
                    'legal_name' => 'Bima Santoso',
                    'email' => 'bima@example.test',
                    'whatsapp' => '+62 813 0000 0000',
                    'student_id' => '',
                ],
                [
                    'role' => 'Member 3 (optional)',
                    'legal_name' => '',
                    'email' => '',
                    'whatsapp' => '',
                    'student_id' => '',
                ],
            ],
            'drive_url' => 'https://drive.google.com/drive/folders/catalyst-demo',
            'payment_sender' => 'Alya Pratama',
            'payment_date' => '2026-09-17',
            'payment_time' => '10:30',
            'payment_note' => '',
        ];

        $registration = match ($scenario) {
            'submitted' => $this->statusState('SUBMITTED', 'WAITING_VERIFICATION', 'locked', 'available', $form),
            'under_review' => $this->statusState('UNDER_REVIEW', 'WAITING_VERIFICATION', 'locked', 'available', $form),
            'revision_required' => $this->statusState('REVISION_REQUIRED', 'REJECTED', 'locked', 'available', $form, [
                'title' => 'Your registration needs an update',
                'description' => 'Catalyst requested a few changes before your registration can be approved.',
                'detail' => 'We couldn’t access your registration folder. Please update the sharing permission and submit again.',
                'tone' => 'warning',
            ]),
            'verified' => $this->statusState('VERIFIED', 'VERIFIED', 'available', 'available', $form, [
                'title' => 'Registration verified',
                'description' => 'Your team registration has been approved. You can continue to submission when the competition stage is open.',
                'tone' => 'success',
            ]),
            'rejected' => $this->statusState('REJECTED', 'REJECTED', 'closed', 'locked', $form, [
                'title' => 'Registration not approved',
                'description' => 'Your registration could not be approved. Review the reason below for more information.',
                'detail' => 'Team members did not meet the eligibility requirements for this competition.',
                'tone' => 'error',
            ]),
            'payment_rejected' => $this->statusState('DRAFT', 'REJECTED', 'locked', 'locked', $form, [
                'title' => 'Payment needs an update',
                'description' => 'The payment information could not be verified. Update it before submitting your registration.',
                'tone' => 'warning',
            ]),
            'payment_waived' => $this->statusState('DRAFT', 'WAIVED', 'locked', 'locked', $form),
            default => $this->statusState('DRAFT', 'NOT_SUBMITTED', 'locked', 'locked', $form),
        };

        return [
            'competition' => $competition,
            ...$registration,
            'submission_route' => $registration['submission']['state'] === 'available'
                ? route('dashboard.submission.show', ['competition' => $competition['slug'], 'stage' => 'stage-1'])
                : null,
            'contact_url' => 'https://example.com/catalyst-contact',
            'whatsapp_url' => 'https://example.com/catalyst-whatsapp-group',
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
                'eligibility' => 'High school and university students.',
                'fee' => 'IDR 75,000',
                'poster_accent' => 'var(--color-catalyst-lime)',
            ],
            'bcc' => [
                'slug' => 'bcc',
                'name' => 'Business Case Competition',
                'short_name' => 'BCC',
                'eligibility' => 'University students.',
                'fee' => 'IDR 100,000',
                'poster_accent' => 'var(--color-catalyst-green)',
            ],
            'bpc' => [
                'slug' => 'bpc',
                'name' => 'Business Plan Competition',
                'short_name' => 'BPC',
                'eligibility' => 'University students.',
                'fee' => 'IDR 100,000',
                'poster_accent' => 'var(--color-catalyst-blue)',
            ],
        ];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function mixedRegistration(string $slug): ?array
    {
        return match ($slug) {
            'mcc' => ['registration_status' => 'VERIFIED', 'payment_status' => 'VERIFIED', 'team_name' => 'Northstar Team'],
            'bpc' => ['registration_status' => 'DRAFT', 'payment_status' => 'NOT_SUBMITTED', 'team_name' => 'Catalyst Collective'],
            default => null,
        };
    }

    /**
     * @param  array<string, mixed>  $competition
     * @param  array<string, mixed>|null  $registration
     * @return array<string, mixed>
     */
    private function normaliseIndexCompetition(array $competition, ?array $registration): array
    {
        $registrationStatus = $registration['registration_status'] ?? 'NOT_REGISTERED';
        $paymentStatus = $registration['payment_status'] ?? 'NOT_SUBMITTED';

        return [
            ...$competition,
            'registered' => $registration !== null,
            'registration_status' => $registrationStatus,
            'registration' => $this->status($registrationStatus),
            'payment_status' => $paymentStatus,
            'payment' => $this->paymentStatus($paymentStatus),
            'team_name' => $registration['team_name'] ?? null,
            'cta_label' => match ($registrationStatus) {
                'DRAFT' => 'Continue Registration',
                'REVISION_REQUIRED' => 'Update Registration',
                'REJECTED' => 'View Details',
                'NOT_REGISTERED' => 'Start Registration',
                default => 'View Registration',
            },
            'cta_route' => route('dashboard.registration.show', ['competition' => $competition['slug']]),
        ];
    }

    /**
     * @param  array<string, mixed>  $form
     * @param  array<string, string>|null  $notice
     * @return array<string, mixed>
     */
    private function statusState(string $registrationStatus, string $paymentStatus, string $submissionAccess, string $whatsappAccess, array $form, ?array $notice = null): array
    {
        return [
            'registration_status' => $registrationStatus,
            'registration' => $this->status($registrationStatus),
            'payment_status' => $paymentStatus,
            'payment' => $this->paymentStatus($paymentStatus),
            'submission' => $this->accessStatus($submissionAccess),
            'whatsapp' => [
                'state' => $whatsappAccess,
                'label' => $whatsappAccess === 'available' ? 'Available' : 'Locked',
            ],
            'form_editable' => in_array($registrationStatus, ['DRAFT', 'REVISION_REQUIRED'], true),
            'payment_required' => $paymentStatus !== 'WAIVED',
            'form' => $form,
            'notice' => $notice ?? $this->statusNotice($registrationStatus),
        ];
    }

    /**
     * @return array<string, string>|null
     */
    private function statusNotice(string $status): ?array
    {
        return match ($status) {
            'SUBMITTED' => [
                'title' => 'Registration submitted',
                'description' => 'Your registration has been sent to the Catalyst team.',
                'tone' => 'info',
            ],
            'UNDER_REVIEW' => [
                'title' => 'Registration under review',
                'description' => 'Your registration is currently being reviewed by the Catalyst team.',
                'tone' => 'info',
            ],
            default => null,
        };
    }

    /**
     * @return array<string, string>
     */
    private function status(string $status): array
    {
        return match ($status) {
            'DRAFT' => ['label' => 'Draft', 'tone' => 'warning'],
            'SUBMITTED' => ['label' => 'Submitted', 'tone' => 'info'],
            'UNDER_REVIEW' => ['label' => 'Under review', 'tone' => 'info'],
            'REVISION_REQUIRED' => ['label' => 'Revision required', 'tone' => 'warning'],
            'VERIFIED' => ['label' => 'Verified', 'tone' => 'success'],
            'REJECTED' => ['label' => 'Not approved', 'tone' => 'error'],
            default => ['label' => 'Not registered', 'tone' => 'neutral'],
        };
    }

    /**
     * @return array<string, string>
     */
    private function paymentStatus(string $status): array
    {
        return match ($status) {
            'WAITING_VERIFICATION' => ['label' => 'Waiting verification', 'tone' => 'info'],
            'VERIFIED' => ['label' => 'Verified', 'tone' => 'success'],
            'REJECTED' => ['label' => 'Rejected', 'tone' => 'error'],
            'WAIVED' => ['label' => 'Waived', 'tone' => 'success'],
            default => ['label' => 'Not submitted', 'tone' => 'neutral'],
        };
    }

    /**
     * @return array<string, string>
     */
    private function accessStatus(string $access): array
    {
        return match ($access) {
            'available' => ['state' => 'available', 'label' => 'Available', 'tone' => 'success'],
            'closed' => ['state' => 'closed', 'label' => 'Closed', 'tone' => 'error'],
            default => ['state' => 'locked', 'label' => 'Locked', 'tone' => 'neutral'],
        };
    }
}
