<?php

namespace App\Console\Commands;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Console\Command;

class GrantAdminRole extends Command
{
    protected $signature = 'catalyst:grant-admin {email : Email address of the existing user}';

    protected $description = 'Grant Catalyst administrator access to an existing user';

    public function handle(): int
    {
        $email = mb_strtolower(trim((string) $this->argument('email')));
        $user = User::query()->whereRaw('LOWER(email) = ?', [$email])->first();

        if (! $user) {
            $this->components->error("No user was found with email {$email}.");

            return self::FAILURE;
        }

        if ($user->isAdmin()) {
            $this->components->info("{$user->email} is already an administrator.");

            return self::SUCCESS;
        }

        if (! $this->confirm("Grant administrator access to {$user->email}?")) {
            $this->components->warn('No changes were made.');

            return self::SUCCESS;
        }

        $user->forceFill(['role' => UserRole::Admin])->save();

        $this->components->info("Administrator access granted to {$user->email}.");

        return self::SUCCESS;
    }
}
