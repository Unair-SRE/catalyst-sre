<?php

use App\Enums\CompetitionCode;
use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
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

test('database seeder creates representative competition data', function () {
    $this->seed();

    expect(Team::query()->count())->toBe(3)
        ->and(TeamMember::query()->count())->toBe(3)
        ->and(User::query()->where('role', UserRole::Admin)->count())->toBe(1)
        ->and(Competition::query()->count())->toBe(3)
        ->and(Registration::query()->count())->toBe(5)
        ->and(Payment::query()->count())->toBe(5)
        ->and(Payment::query()->where('status', PaymentStatus::Verified)->count())->toBe(2)
        ->and(Payment::query()->where('status', PaymentStatus::WaitingVerification)->count())->toBe(1)
        ->and(Payment::query()->where('status', PaymentStatus::Rejected)->count())->toBe(1)
        ->and(Payment::query()->whereNull('status')->count())->toBe(1)
        ->and(Registration::query()->where('status', RegistrationStatus::Pending)->count())->toBe(2)
        ->and(Registration::query()->where('status', RegistrationStatus::Verified)->count())->toBe(2)
        ->and(Registration::query()->where('status', RegistrationStatus::Rejected)->count())->toBe(1)
        ->and(Competition::query()->where('code', CompetitionCode::MiniCase)->value('registration_open'))->toBeTrue()
        ->and(Competition::query()->where('code', CompetitionCode::BusinessPlan)->value('registration_open'))->toBeFalse()
        ->and(Team::query()->where('name', 'Northstar Team')->firstOrFail()->members)->toHaveCount(0)
        ->and(Team::query()->where('name', 'Catalyst Collective')->firstOrFail()->members)->toHaveCount(1)
        ->and(Team::query()->where('name', 'Garuda Muda')->firstOrFail()->members)->toHaveCount(2)
        ->and(Team::query()->whereNotNull('locked_at')->count())->toBe(3)
        ->and(PaymentSetting::query()->where('is_active', true)->value('contact_person_name'))->toBe('Nadia Catalyst');
});

test('database seeder can be run repeatedly without duplicating teams', function () {
    $this->seed();
    $this->seed();

    expect(Team::query()->count())->toBe(3)
        ->and(TeamMember::query()->count())->toBe(3)
        ->and(User::query()->count())->toBe(4)
        ->and(Competition::query()->count())->toBe(3)
        ->and(Registration::query()->count())->toBe(5)
        ->and(Payment::query()->count())->toBe(5)
        ->and(PaymentSetting::query()->count())->toBe(1);
});
