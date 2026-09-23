<?php

namespace Database\Seeders;

use App\Enums\PaymentStatus;
use App\Enums\UserRole;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CompetitionPaymentSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $admin = User::query()->where('role', UserRole::Admin)->firstOrFail();
        $imageKitEndpoint = rtrim(
            (string) (config('services.imagekit.url_endpoint') ?: 'https://ik.imagekit.io/catalyst-demo'),
            '/',
        );

        $examples = [
            'Northstar Team|MCC' => [
                'sender_name' => 'Alya Pratama',
                'status' => PaymentStatus::Verified,
                'file' => 'northstar-mcc-payment',
                'verified_by' => $admin->id,
                'verified_at' => now()->subDays(3),
            ],
            'Northstar Team|BCC' => [
                'sender_name' => 'Alya Pratama',
                'status' => PaymentStatus::WaitingVerification,
                'file' => 'northstar-bcc-payment',
                'verified_by' => null,
                'verified_at' => null,
            ],
            'Catalyst Collective|BPC' => [
                'sender_name' => 'Bima Santoso',
                'status' => PaymentStatus::Rejected,
                'file' => 'catalyst-collective-bpc-payment',
                'verified_by' => $admin->id,
                'verified_at' => now()->subDay(),
            ],
            'Garuda Muda|MCC' => null,
            'Garuda Muda|BPC' => [
                'sender_name' => 'Dedi Saputra',
                'status' => PaymentStatus::Verified,
                'file' => 'garuda-muda-bpc-payment',
                'verified_by' => $admin->id,
                'verified_at' => now()->subHours(12),
            ],
        ];

        Payment::query()
            ->with(['registration.team', 'registration.competition'])
            ->get()
            ->each(function (Payment $payment) use ($examples, $imageKitEndpoint): void {
                $registration = $payment->registration;
                $key = $registration->team->name.'|'.$registration->competition->code->value;
                $example = $examples[$key] ?? null;

                if ($example === null) {
                    $payment->update([
                        'sender_name' => null,
                        'payment_proof_url' => null,
                        'payment_proof_file_id' => null,
                        'status' => null,
                        'verified_by' => null,
                        'verified_at' => null,
                    ]);

                    return;
                }

                $payment->update([
                    'sender_name' => $example['sender_name'],
                    'payment_proof_url' => "{$imageKitEndpoint}/catalyst/competition-payments/{$example['file']}.jpg",
                    'payment_proof_file_id' => 'seed-'.$example['file'],
                    'status' => $example['status'],
                    'verified_by' => $example['verified_by'],
                    'verified_at' => $example['verified_at'],
                ]);

                $registration->team->lock();
            });
    }
}
