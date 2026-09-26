<?php

use App\Enums\CompetitionCode;
use App\Enums\UserRole;
use App\Models\Competition;
use App\Models\Payment;
use App\Models\PaymentSetting;
use App\Models\Registration;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('database seeder creates only competitions and the admin account', function () {
    $this->seed();

    expect(User::query()->count())->toBe(1)
        ->and(User::query()->where('role', UserRole::Admin)->count())->toBe(1)
        ->and(Competition::query()->count())->toBe(3)
        ->and(Team::query()->count())->toBe(0)
        ->and(TeamMember::query()->count())->toBe(0)
        ->and(Registration::query()->count())->toBe(0)
        ->and(Payment::query()->count())->toBe(0)
        ->and(PaymentSetting::query()->count())->toBe(0)
        ->and(Competition::query()->where('code', CompetitionCode::MiniCase)->value('registration_open'))->toBeTrue()
        ->and(Competition::query()->where('code', CompetitionCode::BusinessPlan)->value('registration_open'))->toBeFalse();
});

test('database seeder can be run repeatedly without duplicating baseline data', function () {
    $this->seed();
    $this->seed();

    expect(User::query()->count())->toBe(1)
        ->and(Competition::query()->count())->toBe(3)
        ->and(Team::query()->count())->toBe(0)
        ->and(TeamMember::query()->count())->toBe(0)
        ->and(Registration::query()->count())->toBe(0)
        ->and(Payment::query()->count())->toBe(0)
        ->and(PaymentSetting::query()->count())->toBe(0);
});
