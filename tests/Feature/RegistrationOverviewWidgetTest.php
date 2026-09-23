<?php

use App\Enums\CompetitionCode;
use App\Enums\PaymentStatus;
use App\Filament\Widgets\RegistrationOverview;
use App\Models\Competition;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('registration overview shows users teams registrations per competition and pending payments', function () {
    $admin = User::factory()->admin()->create();
    User::factory()->count(2)->create();
    $competition = Competition::factory()->create([
        'code' => CompetitionCode::MiniCase,
        'name' => 'Mini Case Competition',
    ]);
    $team = Team::factory()->create();
    $registration = Registration::factory()->for($team)->for($competition)->create();
    Payment::factory()->for($registration)->create(['status' => PaymentStatus::WaitingVerification]);

    Livewire::actingAs($admin)
        ->test(RegistrationOverview::class)
        ->assertSee('Total Users')
        ->assertSee('Total Teams')
        ->assertSee('MCC Registrations')
        ->assertSee('Payments Awaiting Verification');
});
