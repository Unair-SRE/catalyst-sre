<?php

namespace App\Support\Dashboard;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

final class DashboardProfileState
{
    /**
     * @return array<string, mixed>
     */
    public function get(): array
    {
        /** @var User $user */
        $user = Auth::user();

        return [
            'profile' => [
                'name' => $user->name,
                'email' => $user->email,
                'whatsapp' => $user->whatsapp ?? '',
                'institution' => 'Universitas Indonesia',
            ],
            'password_policy' => [
                'minimum_length' => 8,
            ],
            'email_notice' => 'Changing your email address requires a new verification code.',
        ];
    }
}
