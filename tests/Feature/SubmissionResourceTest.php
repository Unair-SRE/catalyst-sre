<?php

use App\Filament\Resources\CompetitionRounds\Pages\CreateCompetitionRound;
use App\Filament\Resources\Submissions\SubmissionResource;
use App\Models\Competition;
use App\Models\CompetitionRound;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();

    config()->set('services.cloudinary.cloud_name', 'demo');
    config()->set('services.cloudinary.api_key', '123456789');
    config()->set('services.cloudinary.api_secret', 'secret');
});

test('administrators can browse and open a submission', function () {
    $registration = Registration::factory()->create();
    $round = CompetitionRound::factory()->for($registration->competition)->create([
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addWeek(),
    ]);
    $submission = Submission::factory()->create([
        'registration_id' => $registration->id,
        'competition_round_id' => $round->id,
        'public_id' => "catalyst/submissions/{$registration->id}/{$round->id}/deck.pdf",
        'submitted_at' => now(),
    ]);

    $this->actingAs($this->admin)
        ->get(SubmissionResource::getUrl('index'))
        ->assertOk()
        ->assertSee($registration->team->name);

    $this->actingAs($this->admin)
        ->get(SubmissionResource::getUrl('view', ['record' => $submission]))
        ->assertOk()
        ->assertSee("catalyst/submissions/{$registration->id}/{$round->id}/deck.pdf");
});

test('a round cannot be saved without a submission window', function () {
    $competition = Competition::factory()->create();

    $this->actingAs($this->admin);

    Livewire::test(CreateCompetitionRound::class)
        ->fillForm([
            'competition_id' => $competition->id,
            'name' => 'Penyisihan',
            'round_order' => 1,
        ])
        ->call('create')
        ->assertHasFormErrors(['starts_at' => 'required', 'ends_at' => 'required']);
});

test('a round window must close after it opens', function () {
    $competition = Competition::factory()->create();

    $this->actingAs($this->admin);

    Livewire::test(CreateCompetitionRound::class)
        ->fillForm([
            'competition_id' => $competition->id,
            'name' => 'Penyisihan',
            'round_order' => 1,
            'starts_at' => now()->addWeek()->toDateTimeString(),
            'ends_at' => now()->toDateTimeString(),
        ])
        ->call('create')
        ->assertHasFormErrors(['ends_at']);
});
