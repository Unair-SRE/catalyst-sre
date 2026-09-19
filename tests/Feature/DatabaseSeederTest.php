<?php

use App\Enums\UserRole;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('database seeder creates three representative teams', function () {
    $this->seed();

    expect(Team::query()->count())->toBe(3)
        ->and(TeamMember::query()->count())->toBe(3)
        ->and(User::query()->where('role', UserRole::Admin)->count())->toBe(1)
        ->and(Team::query()->where('name', 'Northstar Team')->firstOrFail()->members)->toHaveCount(0)
        ->and(Team::query()->where('name', 'Catalyst Collective')->firstOrFail()->members)->toHaveCount(1)
        ->and(Team::query()->where('name', 'Garuda Muda')->firstOrFail()->members)->toHaveCount(2);
});

test('database seeder can be run repeatedly without duplicating teams', function () {
    $this->seed();
    $this->seed();

    expect(Team::query()->count())->toBe(3)
        ->and(TeamMember::query()->count())->toBe(3)
        ->and(User::query()->count())->toBe(4);
});
