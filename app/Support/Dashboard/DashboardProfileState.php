<?php

namespace App\Support\Dashboard;

final class DashboardProfileState
{
    /**
     * @return array<string, mixed>
     */
    public function get(): array
    {
        return [
            'profile' => [
                'name' => 'Alya Pratama',
                'email' => 'alya@example.test',
                'whatsapp' => '+62 812 0000 0000',
                'institution' => 'Universitas Indonesia',
            ],
            'password_policy' => [
                'minimum_length' => 8,
            ],
            'email_notice' => 'Email verification will be required when account email changes are connected to production authentication.',
        ];
    }
}
