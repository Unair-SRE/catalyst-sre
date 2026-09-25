<?php

namespace App\Rules;

use App\Models\TeamMember;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class AvailableTeamEmail implements ValidationRule
{
    public function __construct(
        private readonly ?int $ignoreTeamMemberId = null,
        private readonly ?int $ignoreUserId = null,
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $email = Str::lower(trim((string) $value));

        $usedByUser = User::query()
            ->when($this->ignoreUserId, fn ($query, int $id) => $query->whereKeyNot($id))
            ->whereRaw('LOWER(email) = ?', [$email])
            ->exists();

        $usedByMember = TeamMember::query()
            ->when($this->ignoreTeamMemberId, fn ($query, int $id) => $query->whereKeyNot($id))
            ->whereRaw('LOWER(email) = ?', [$email])
            ->exists();

        if ($usedByUser || $usedByMember) {
            $fail('This email is already used by an account or team member.');
        }
    }
}
