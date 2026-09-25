<?php

use App\Enums\CompetitionCode;
use App\Enums\PaymentStatus;
use App\Livewire\Dashboard\CompetitionRegistrationDetail;
use App\Livewire\Dashboard\CompetitionRegistrationIndex;
use App\Livewire\Dashboard\DatabaseOverview;
use App\Models\Competition;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('participant authentication pages render complete forms', function () {
    $this->get('/login')->assertOk()->assertSee('Welcome back')->assertSee('Forgot password?');
    $this->get('/register')->assertOk()->assertSee('WhatsApp number')->assertSee('Confirm password');
    $this->get('/forgot-password')->assertOk()->assertSee('Send reset link');

    $user = User::factory()->unverified()->create();
    $this->actingAs($user)->get('/email/verify')->assertOk()->assertSee('Verification code')->assertSee('Resend code');
});

test('public competition catalog handles empty and database states', function () {
    $this->get(route('competitions.index'))->assertOk()->assertSee('Competition information is coming soon');

    Competition::factory()->code(CompetitionCode::MiniCase)->create([
        'description' => 'A database-backed public description.',
        'registration_open' => true,
    ]);

    $this->get(route('competitions.index'))
        ->assertOk()
        ->assertSee('Mini Case Competition')
        ->assertSee('A database-backed public description.')
        ->assertSee('Create account to register');
});

test('registration index displays real registration and payment states', function () {
    $team = Team::factory()->create();
    $competition = Competition::factory()->code(CompetitionCode::MiniCase)->create();
    $registration = Registration::factory()->for($team)->for($competition)->create();
    Payment::factory()->for($registration)->create(['status' => PaymentStatus::Rejected]);

    Livewire::actingAs($team->captain)
        ->test(CompetitionRegistrationIndex::class)
        ->assertSee('Pending')
        ->assertSee('Rejected')
        ->assertSee('View Registration');
});

test('registration cards render competition information from the database', function () {
    $team = Team::factory()->create();
    Competition::factory()->code(CompetitionCode::BusinessCase)->create([
        'name' => 'Database Competition Name',
        'description' => 'Content maintained in the competition record.',
        'registration_fee' => 123456,
        'registration_open' => true,
        'registration_start_at' => now()->subDay(),
        'registration_end_at' => now()->addWeek(),
    ]);

    Livewire::actingAs($team->captain)
        ->test(CompetitionRegistrationIndex::class)
        ->assertSee('Database Competition Name')
        ->assertSee('Content maintained in the competition record.')
        ->assertSee('IDR 123.456');
});

test('registration cards respect the database availability switch', function () {
    $team = Team::factory()->create();
    Competition::factory()->code(CompetitionCode::BusinessPlan)->create([
        'registration_open' => false,
        'registration_start_at' => now()->addDay(),
        'registration_end_at' => now()->addWeek(),
    ]);

    Livewire::actingAs($team->captain)
        ->test(CompetitionRegistrationIndex::class)
        ->assertSee('Registration closed')
        ->assertDontSee('Opens ');
});

test('registration detail blocks incomplete teams and creates a real registration for complete teams', function () {
    $team = Team::factory()->create();
    $competition = Competition::factory()->code(CompetitionCode::MiniCase)->create([
        'registration_open' => true,
        'registration_start_at' => now()->subDay(),
        'registration_end_at' => now()->addDay(),
    ]);

    Livewire::actingAs($team->captain)
        ->test(CompetitionRegistrationDetail::class, ['competition' => 'mcc'])
        ->assertSee('Upload the captain and every member KTM')
        ->assertDontSee('Register this team');

    $team->captain->update([
        'ktm_url' => 'https://ik.imagekit.io/catalyst/ktm/captain.jpg',
        'ktm_file_id' => 'captain-ktm',
    ]);

    Livewire::actingAs($team->captain)
        ->test(CompetitionRegistrationDetail::class, ['competition' => 'mcc'])
        ->assertSee('Register this team')
        ->call('openConfirmation')
        ->assertSet('showConfirm', true)
        ->call('submitRegistration')
        ->assertHasNoErrors()
        ->assertSee('Registration created');

    $this->assertDatabaseHas('registrations', [
        'team_id' => $team->id,
        'competition_id' => $competition->id,
    ]);
});

test('database dashboard renders empty and action-required states', function () {
    $user = User::factory()->create(['name' => 'Eka Participant']);

    Livewire::actingAs($user)
        ->test(DatabaseOverview::class)
        ->assertSee('Hi,')
        ->assertSee('Eka Participant')
        ->assertSee('Create your team')
        ->assertSee('No competitions yet');

    $team = Team::factory()->for($user, 'captain')->create();
    $competition = Competition::factory()->code(CompetitionCode::BusinessCase)->create();
    $registration = Registration::factory()->for($team)->for($competition)->create();
    $registration->payment()->create();

    Livewire::actingAs($user)
        ->test(DatabaseOverview::class)
        ->assertSee('Complete team documents')
        ->assertSee('Complete BCC payment')
        ->assertSee('Business Case Competition');
});

test('locked team UI hides participant editing controls', function () {
    $team = Team::factory()->locked()->create();

    $this->actingAs($team->captain)
        ->get(route('dashboard.team.index'))
        ->assertOk()
        ->assertSee('Team data is locked')
        ->assertDontSee('Save team')
        ->assertDontSee('Add member');
});
