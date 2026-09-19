<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->user([
            'name' => 'Catalyst Admin',
            'email' => 'admin@example.com',
            'whatsapp' => '081200000000',
            'role' => UserRole::Admin,
            'password' => 'catalost',
        ]);

        $northstar = $this->team(
            captain: [
                'name' => 'Alya Pratama',
                'email' => 'alya@example.com',
                'whatsapp' => '081211110001',
                'ktm_file_id' => 'seed-ktm-alya',
            ],
            team: [
                'name' => 'Northstar Team',
                'institution' => 'Universitas Indonesia',
            ],
        );

        $this->members($northstar, []);

        $catalystCollective = $this->team(
            captain: [
                'name' => 'Bima Santoso',
                'email' => 'bima@example.com',
                'whatsapp' => '081211110002',
                'ktm_file_id' => 'seed-ktm-bima',
            ],
            team: [
                'name' => 'Catalyst Collective',
                'institution' => 'Institut Teknologi Bandung',
            ],
        );

        $this->members($catalystCollective, [
            [
                'name' => 'Citra Lestari',
                'email' => 'citra.member@example.com',
                'whatsapp' => '081322220001',
                'ktm_file_id' => 'seed-ktm-citra',
            ],
        ]);

        $garudaMuda = $this->team(
            captain: [
                'name' => 'Dedi Saputra',
                'email' => 'dedi@example.com',
                'whatsapp' => '081211110003',
                'ktm_file_id' => 'seed-ktm-dedi',
            ],
            team: [
                'name' => 'Garuda Muda',
                'institution' => 'Universitas Gadjah Mada',
            ],
        );

        $this->members($garudaMuda, [
            [
                'name' => 'Erika Putri',
                'email' => 'erika.member@example.com',
                'whatsapp' => '081322220002',
                'ktm_file_id' => 'seed-ktm-erika',
            ],
            [
                'name' => 'Farhan Akbar',
                'email' => 'farhan.member@example.com',
                'whatsapp' => '081322220003',
                'ktm_file_id' => 'seed-ktm-farhan',
            ],
        ]);
    }

    /** @param array{name: string, email: string, whatsapp: string, role?: UserRole, ktm_file_id?: string, password?: string} $data */
    private function user(array $data): User
    {
        $user = User::query()->firstOrNew(['email' => $data['email']]);
        $ktmFileId = $data['ktm_file_id'] ?? null;

        $user->forceFill([
            'name' => $data['name'],
            'whatsapp' => $data['whatsapp'],
            'email_verified_at' => now(),
            'password' => $data['password'] ?? 'password',
            'role' => $data['role'] ?? UserRole::Participant,
            'ktm_url' => $ktmFileId ? "https://example.test/catalyst/ktm/{$ktmFileId}.jpg" : null,
            'ktm_file_id' => $ktmFileId,
        ])->save();

        return $user;
    }

    /**
     * @param  array{name: string, email: string, whatsapp: string, ktm_file_id: string}  $captain
     * @param  array{name: string, institution: string}  $team
     */
    private function team(array $captain, array $team): Team
    {
        $captain = $this->user($captain);

        return Team::query()->updateOrCreate(
            ['captain_id' => $captain->id],
            [...$team, 'locked_at' => null],
        );
    }

    /**
     * @param  array<int, array{name: string, email: string, whatsapp: string, ktm_file_id: string}>  $members
     */
    private function members(Team $team, array $members): void
    {
        foreach ($members as $member) {
            TeamMember::query()->updateOrCreate(
                ['email' => $member['email']],
                [
                    'team_id' => $team->id,
                    'name' => $member['name'],
                    'whatsapp' => $member['whatsapp'],
                    'ktm_url' => "https://example.test/catalyst/ktm/{$member['ktm_file_id']}.jpg",
                    'ktm_file_id' => $member['ktm_file_id'],
                ],
            );
        }
    }
}
