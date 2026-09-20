<?php

use App\Actions\Registrations\RegisterTeam;
use App\Enums\CompetitionCode;
use App\Enums\RegistrationStatus;
use App\Filament\Resources\Competitions\CompetitionResource;
use App\Filament\Resources\Registrations\RegistrationResource;
use App\Models\Competition;
use App\Models\Registration;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

function registrationReadyTeam(array $teamOverrides = []): Team
{
    $captain = User::factory()->create([
        'ktm_url' => 'https://example.test/captain-ktm.jpg',
        'ktm_file_id' => 'captain-ktm',
    ]);

    return Team::factory()->for($captain, 'captain')->create($teamOverrides);
}

test('captain can register a complete team for mini case and one main competition', function () {
    $team = registrationReadyTeam();
    TeamMember::factory()->for($team)->create();
    $miniCase = Competition::factory()->code(CompetitionCode::MiniCase)->create();
    $businessCase = Competition::factory()->code(CompetitionCode::BusinessCase)->create();

    $miniRegistration = app(RegisterTeam::class)->handle($team->captain, $team, $miniCase);
    $mainRegistration = app(RegisterTeam::class)->handle($team->captain, $team, $businessCase);

    expect($miniRegistration->status)->toBe(RegistrationStatus::Pending)
        ->and($mainRegistration->status)->toBe(RegistrationStatus::Pending)
        ->and($team->registrations()->count())->toBe(2);
});

test('the same team cannot register for the same competition twice', function () {
    $team = registrationReadyTeam();
    $competition = Competition::factory()->code(CompetitionCode::MiniCase)->create();

    app(RegisterTeam::class)->handle($team->captain, $team, $competition);
    app(RegisterTeam::class)->handle($team->captain, $team, $competition);
})->throws(ValidationException::class, 'already registered');

test('a team cannot enter both main competitions', function () {
    $team = registrationReadyTeam();
    $businessCase = Competition::factory()->code(CompetitionCode::BusinessCase)->create();
    $businessPlan = Competition::factory()->code(CompetitionCode::BusinessPlan)->create();

    app(RegisterTeam::class)->handle($team->captain, $team, $businessCase);
    app(RegisterTeam::class)->handle($team->captain, $team, $businessPlan);
})->throws(ValidationException::class, 'only one main competition');

test('registration rejects a team when any KTM is missing', function () {
    $team = registrationReadyTeam();
    $team->captain->forceFill(['ktm_url' => null, 'ktm_file_id' => null])->save();
    $competition = Competition::factory()->code(CompetitionCode::MiniCase)->create();

    app(RegisterTeam::class)->handle($team->captain, $team, $competition);
})->throws(ValidationException::class, 'must have a KTM');

test('registration rejects a closed competition', function () {
    $team = registrationReadyTeam();
    $competition = Competition::factory()->code(CompetitionCode::MiniCase)->closed()->create();

    app(RegisterTeam::class)->handle($team->captain, $team, $competition);
})->throws(ValidationException::class, 'is closed');

test('a participant cannot register another captain team', function () {
    $team = registrationReadyTeam();
    $outsider = User::factory()->create();
    $competition = Competition::factory()->code(CompetitionCode::MiniCase)->create();

    app(RegisterTeam::class)->handle($outsider, $team, $competition);
})->throws(ValidationException::class, 'Only the team captain');

test('an unverified user cannot create a registration', function () {
    $captain = User::factory()->unverified()->create([
        'ktm_url' => 'https://example.test/captain-ktm.jpg',
        'ktm_file_id' => 'captain-ktm',
    ]);
    $team = Team::factory()->for($captain, 'captain')->create();
    $competition = Competition::factory()->code(CompetitionCode::MiniCase)->create();

    app(RegisterTeam::class)->handle($captain, $team, $competition);
})->throws(AuthorizationException::class);

test('database enforces unique team and competition registration', function () {
    $team = registrationReadyTeam();
    $competition = Competition::factory()->code(CompetitionCode::MiniCase)->create();

    Registration::factory()->for($team)->for($competition)->create();
    Registration::factory()->for($team)->for($competition)->create();
})->throws(QueryException::class);

test('admin can manage competitions and inspect registrations in filament', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)->get(CompetitionResource::getUrl('index'))->assertOk();
    $this->actingAs($admin)->get(RegistrationResource::getUrl('index'))->assertOk();
});

test('participants cannot access competition and registration filament resources', function () {
    $participant = User::factory()->create();

    $this->actingAs($participant)->get(CompetitionResource::getUrl('index'))->assertForbidden();
    $this->actingAs($participant)->get(RegistrationResource::getUrl('index'))->assertForbidden();
});
