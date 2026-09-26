<?php

namespace Database\Seeders;

use App\Enums\CompetitionCode;
use App\Enums\UserRole;
use App\Models\Competition;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->seedCompetitions();
        $this->seedAdmin();
    }

    private function seedAdmin(): void
    {
        $admin = User::query()->firstOrNew(['email' => 'admin@example.com']);

        $admin->forceFill([
            'name' => 'Catalyst Admin',
            'whatsapp' => '081200000000',
            'email_verified_at' => now(),
            'password' => 'catalost',
            'role' => UserRole::Admin,
            'ktm_url' => null,
            'ktm_file_id' => null,
        ])->save();
    }

    private function seedCompetitions(): void
    {
        $data = [
            CompetitionCode::MiniCase->value => [
                'description' => 'A compact case-solving competition for students to demonstrate structured thinking and practical insight.',
                'registration_fee' => 75000,
                'registration_open' => true,
                'registration_start_at' => now()->subDays(7),
                'registration_end_at' => now()->addDays(21),
            ],
            CompetitionCode::BusinessCase->value => [
                'description' => 'Teams analyse a real-world business challenge and present an evidence-based strategic recommendation.',
                'registration_fee' => 150000,
                'registration_open' => true,
                'registration_start_at' => now()->subDays(5),
                'registration_end_at' => now()->addDays(30),
            ],
            CompetitionCode::BusinessPlan->value => [
                'description' => 'Teams develop and present a viable, innovative, and sustainable business plan.',
                'registration_fee' => 150000,
                'registration_open' => false,
                'registration_start_at' => now()->subDays(45),
                'registration_end_at' => now()->subDays(7),
            ],
        ];

        foreach (CompetitionCode::cases() as $code) {
            Competition::query()->updateOrCreate(
                ['code' => $code],
                [
                    'name' => $code->label(),
                    ...$data[$code->value],
                ],
            );
        }
    }
}
