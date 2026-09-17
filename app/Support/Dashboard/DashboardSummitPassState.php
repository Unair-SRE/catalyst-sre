<?php

namespace App\Support\Dashboard;

use Carbon\CarbonImmutable;

final class DashboardSummitPassState
{
    /**
     * @return array<string, string>
     */
    public static function scenarios(): array
    {
        return [
            'no_pass' => 'No pass',
            'purchase' => 'Purchase',
            'payment_waiting' => 'Payment waiting',
            'rejected' => 'Payment rejected',
            'verified' => 'Verified',
            'checked_in' => 'Checked in',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function for(string $scenario): array
    {
        $scenario = array_key_exists($scenario, self::scenarios()) ? $scenario : 'no_pass';

        $state = [
            'scenario' => $scenario,
            'status' => 'NO_PASS',
            'attendee' => [
                'name' => 'Alya Pratama',
                'email' => 'alya@example.test',
                'whatsapp' => '+62 812 0000 0000',
                'institution' => 'Universitas Indonesia',
            ],
            'event' => [
                'name' => 'Catalyst Summit',
                'access' => 'Talkshow + Exhibition',
                'starts_at' => '2026-11-22 09:00:00',
                'venue' => 'Venue to be announced',
                'price' => 'IDR 150,000',
            ],
            'payment' => [
                'sender_name' => 'Alya Pratama',
                'date' => '2026-10-04',
                'time' => '14:30',
                'proof' => null,
            ],
            'ticket' => null,
            'submitted_at' => null,
            'checked_in_at' => null,
            'rejection_reason' => null,
            'history' => [],
        ];

        $state = match ($scenario) {
            'purchase' => array_replace($state, ['status' => 'PURCHASE']),
            'payment_waiting' => array_replace($state, [
                'status' => 'WAITING_VERIFICATION',
                'payment' => [
                    ...$state['payment'],
                    'proof' => $this->proof('summit-pass-payment.pdf', 'application/pdf', 840_000),
                ],
                'submitted_at' => '2026-10-04 14:34:00',
                'history' => [
                    $this->history('Payment submitted', '2026-10-04 14:34:00', 'Payment is waiting for Catalyst review.'),
                ],
            ]),
            'rejected' => array_replace($state, [
                'status' => 'REJECTED',
                'payment' => [
                    ...$state['payment'],
                    'proof' => $this->proof('summit-pass-payment.pdf', 'application/pdf', 840_000),
                ],
                'submitted_at' => '2026-10-04 14:34:00',
                'rejection_reason' => 'The uploaded payment proof could not be verified.',
                'history' => [
                    $this->history('Payment submitted', '2026-10-04 14:34:00', 'Payment proof received.'),
                    $this->history('Update requested', '2026-10-05 09:15:00', 'Payment proof could not be verified.'),
                ],
            ]),
            'verified' => array_replace($state, [
                'status' => 'VERIFIED',
                'ticket' => $this->ticket(),
                'submitted_at' => '2026-10-04 14:34:00',
                'history' => [
                    $this->history('Payment verified', '2026-10-05 10:00:00', 'Summit Pass activated.'),
                ],
            ]),
            'checked_in' => array_replace($state, [
                'status' => 'CHECKED_IN',
                'ticket' => $this->ticket(),
                'submitted_at' => '2026-10-04 14:34:00',
                'checked_in_at' => '2026-11-22 08:42:00',
                'history' => [
                    $this->history('Payment verified', '2026-10-05 10:00:00', 'Summit Pass activated.'),
                    $this->history('Checked in', '2026-11-22 08:42:00', 'Ticket scanned by the Catalyst check-in team.'),
                ],
            ]),
            default => $state,
        };

        return [
            ...$state,
            'status_display' => $this->status($state['status']),
            'event' => [
                ...$state['event'],
                'date_label' => CarbonImmutable::parse($state['event']['starts_at'], 'Asia/Jakarta')->format('d F Y'),
                'time_label' => CarbonImmutable::parse($state['event']['starts_at'], 'Asia/Jakarta')->format('H:i').' WIB',
            ],
            'submitted_label' => $state['submitted_at'] ? $this->dateTimeLabel($state['submitted_at']) : null,
            'checked_in_label' => $state['checked_in_at'] ? $this->dateTimeLabel($state['checked_in_at']) : null,
        ];
    }

    /**
     * @return array<string, string>
     */
    public function status(string $status): array
    {
        return match ($status) {
            'PURCHASE', 'DRAFT' => ['label' => 'Purchase in progress', 'tone' => 'info'],
            'WAITING_VERIFICATION' => ['label' => 'Payment under review', 'tone' => 'info'],
            'REJECTED' => ['label' => 'Payment needs an update', 'tone' => 'error'],
            'VERIFIED' => ['label' => 'Verified', 'tone' => 'success'],
            'CHECKED_IN' => ['label' => 'Checked In', 'tone' => 'success'],
            'CANCELLED' => ['label' => 'Cancelled', 'tone' => 'error'],
            default => ['label' => 'No pass', 'tone' => 'neutral'],
        };
    }

    /**
     * @return array<string, string>
     */
    public function history(string $event, string $timestamp, string $detail): array
    {
        return [
            'event' => $event,
            'timestamp' => $timestamp,
            'timestamp_label' => $this->dateTimeLabel($timestamp),
            'detail' => $detail,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function ticket(): array
    {
        return [
            'id' => 'CAT-26-00421',
            'qr_label' => 'Prototype QR / Ticket QR Placeholder',
            'check_in_status' => 'Not checked in yet',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function proof(string $name, string $type, int $sizeBytes): array
    {
        return [
            'name' => $name,
            'type' => $type,
            'size_bytes' => $sizeBytes,
            'size_label' => number_format($sizeBytes / 1024 / 1024, 1).' MB',
        ];
    }

    private function dateTimeLabel(string $value): string
    {
        return CarbonImmutable::parse($value, 'Asia/Jakarta')->format('d F Y · H:i').' WIB';
    }
}
