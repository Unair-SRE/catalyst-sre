<?php

use App\Actions\Results\PublishCompetitionRound;
use App\Actions\Results\SetQualificationResult;
use App\Enums\CompetitionCode;
use App\Enums\QualificationStatus;
use App\Enums\RegistrationStatus;
use App\Enums\UserRole;
use App\Models\Competition;
use App\Models\CompetitionRound;
use App\Models\Registration;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

function resultAdmin(): User
{
    return User::factory()->create(['email_verified_at' => now(), 'role' => UserRole::Admin]);
}

function verifiedRegistrationFixture(): Registration
{
    $team = Team::factory()->create();
    $competition = Competition::factory()->create(['code' => CompetitionCode::BusinessCase]);

    return Registration::factory()->for($team)->for($competition)->create([
        'status' => RegistrationStatus::Verified,
    ]);
}

function roundFixture(Competition $competition, int $order, string $name = 'Penyisihan'): CompetitionRound
{
    return CompetitionRound::factory()->for($competition)->create([
        'name' => $name,
        'round_order' => $order,
    ]);
}

test('an admin records a qualification result for a verified registration', function () {
    $admin = resultAdmin();
    $registration = verifiedRegistrationFixture();
    $round = roundFixture($registration->competition, 1);

    $result = app(SetQualificationResult::class)->handle(
        $admin, $registration, $round, QualificationStatus::Qualified,
    );

    expect($result->status)->toBe(QualificationStatus::Qualified)
        ->and($result->decided_by)->toBe($admin->id)
        ->and($result->decided_at)->not->toBeNull();
});

test('recording the same round twice updates instead of duplicating', function () {
    $admin = resultAdmin();
    $registration = verifiedRegistrationFixture();
    $round = roundFixture($registration->competition, 1);

    app(SetQualificationResult::class)->handle($admin, $registration, $round, QualificationStatus::Qualified);
    app(SetQualificationResult::class)->handle($admin, $registration, $round, QualificationStatus::NotQualified);

    expect($registration->qualificationResults()->count())->toBe(1)
        ->and($registration->qualificationResults()->firstOrFail()->status)->toBe(QualificationStatus::NotQualified);
});

test('a result cannot be recorded for an unverified registration', function () {
    $registration = verifiedRegistrationFixture();
    $registration->update(['status' => RegistrationStatus::Pending]);
    $round = roundFixture($registration->competition, 1);

    app(SetQualificationResult::class)->handle(
        resultAdmin(), $registration, $round, QualificationStatus::Qualified,
    );
})->throws(ValidationException::class);

test('a result cannot skip a failed previous round', function () {
    $admin = resultAdmin();
    $registration = verifiedRegistrationFixture();
    $first = roundFixture($registration->competition, 1, 'Penyisihan');
    $second = roundFixture($registration->competition, 2, 'Semifinal');

    app(SetQualificationResult::class)->handle($admin, $registration, $first, QualificationStatus::NotQualified);

    app(SetQualificationResult::class)->handle($admin, $registration, $second, QualificationStatus::Qualified);
})->throws(ValidationException::class);

test('a result is allowed in the next round after qualifying', function () {
    $admin = resultAdmin();
    $registration = verifiedRegistrationFixture();
    $first = roundFixture($registration->competition, 1, 'Penyisihan');
    $second = roundFixture($registration->competition, 2, 'Semifinal');

    app(SetQualificationResult::class)->handle($admin, $registration, $first, QualificationStatus::Qualified);
    $result = app(SetQualificationResult::class)->handle($admin, $registration, $second, QualificationStatus::Qualified);

    expect($result->status)->toBe(QualificationStatus::Qualified);
});

test('a round from another competition is rejected', function () {
    $admin = resultAdmin();
    $registration = verifiedRegistrationFixture();
    $other = Competition::factory()->create(['code' => CompetitionCode::BusinessPlan]);
    $round = roundFixture($other, 1);

    app(SetQualificationResult::class)->handle($admin, $registration, $round, QualificationStatus::Qualified);
})->throws(ValidationException::class);

test('publishing a round stamps time and publisher exactly once', function () {
    $admin = resultAdmin();
    $registration = verifiedRegistrationFixture();
    $round = roundFixture($registration->competition, 1);

    [$published, $didPublish] = app(PublishCompetitionRound::class)->handle($admin, $round);

    expect($didPublish)->toBeTrue()
        ->and($published->isPublished())->toBeTrue()
        ->and($published->published_by)->toBe($admin->id);

    [$again, $didPublishAgain] = app(PublishCompetitionRound::class)->handle($admin, $round);

    expect($didPublishAgain)->toBeFalse()
        ->and($again->id)->toBe($published->id);
});

test('a non-admin cannot record or publish results', function () {
    $outsider = User::factory()->create(['email_verified_at' => now()]);
    $registration = verifiedRegistrationFixture();
    $round = roundFixture($registration->competition, 1);

    app(SetQualificationResult::class)->handle($outsider, $registration, $round, QualificationStatus::Qualified);
})->throws(AuthorizationException::class);
