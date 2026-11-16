<?php

use App\Contracts\SubmissionFile;
use App\Contracts\SubmissionFileInspector;
use App\Enums\CompetitionCode;
use App\Enums\RegistrationStatus;
use App\Livewire\Dashboard\Overview;
use App\Livewire\Dashboard\RegistrationDetail;
use App\Livewire\Dashboard\SubmissionDetail;
use App\Livewire\Dashboard\SubmissionIndex;
use App\Models\Competition;
use App\Models\CompetitionRound;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\Fakes\FakeSubmissionInspector;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);

    $this->inspector = new FakeSubmissionInspector;
    $this->app->instance(SubmissionFileInspector::class, $this->inspector);

    config()->set('services.cloudinary.cloud_name', 'demo');
    config()->set('services.cloudinary.api_key', '123456789');
    config()->set('services.cloudinary.api_secret', 'secret');
});

function submissionParticipation(
    User $user,
    CompetitionCode $code,
    RegistrationStatus $status = RegistrationStatus::Verified,
): Registration {
    $team = Team::factory()->create(['captain_id' => $user->id]);
    $competition = Competition::factory()->code($code)->create();

    return Registration::factory()->for($team)->for($competition)->create(['status' => $status]);
}

function submissionRound(Registration $registration, int $order = 1, ?string $startsAt = '-1 day', ?string $endsAt = '+1 week'): CompetitionRound
{
    return CompetitionRound::factory()->for($registration->competition)->create([
        'name' => 'Stage '.$order,
        'round_order' => $order,
        'starts_at' => $startsAt === null ? null : now()->modify($startsAt),
        'ends_at' => $endsAt === null ? null : now()->modify($endsAt),
    ]);
}

test('submission index route renders', function () {
    $this->get(route('dashboard.submission.index'))
        ->assertOk()
        ->assertSee('Manage your competition submissions and deadlines');
});

test('submission detail route renders for a configured round', function () {
    $registration = submissionParticipation($this->user, CompetitionCode::BusinessPlan);
    $round = submissionRound($registration);

    $this->get(route('dashboard.submission.show', ['competition' => 'bpc', 'stage' => $round->round_order]))
        ->assertOk()
        ->assertSee('Business Plan Competition')
        ->assertSee('Upload your file');
});

test('submission routes reject invalid competitions and stages', function () {
    $this->get('/dashboard/submission/invalid')->assertNotFound();

    $registration = submissionParticipation($this->user, CompetitionCode::BusinessPlan);
    submissionRound($registration);

    $this->get('/dashboard/submission/bpc/99')->assertNotFound();
});

test('a competition the user did not register for is not reachable', function () {
    $this->get('/dashboard/submission/bpc/1')->assertNotFound();
});

test('no-registration submission index shows its empty state', function () {
    Livewire::test(SubmissionIndex::class)
        ->assertSee('No submissions yet')
        ->assertSee('View Registration');
});

test('an unverified registration locks its rounds', function () {
    $registration = submissionParticipation($this->user, CompetitionCode::BusinessCase, RegistrationStatus::Pending);
    submissionRound($registration);

    Livewire::test(SubmissionIndex::class)
        ->assertSee('Business Case Competition')
        ->assertSee('Registration verification required')
        ->assertSee('Locked');
});

test('verified rounds show open and submitted states', function () {
    $registration = submissionParticipation($this->user, CompetitionCode::MiniCase);
    $openRound = submissionRound($registration);
    $submittedRound = submissionRound($registration, 2);
    Submission::factory()->create([
        'registration_id' => $registration->id,
        'competition_round_id' => $submittedRound->id,
        'version' => 1,
        'submitted_at' => now()->subHour(),
    ]);

    Livewire::test(SubmissionIndex::class)
        ->assertSee('Mini Case Competition')
        ->assertSee('Submitted')
        ->assertSee('Version 1')
        ->assertSee(route('dashboard.submission.show', ['competition' => 'mcc', 'stage' => $openRound->round_order]));
});

test('rounds outside their window show upcoming and closed states', function () {
    $registration = submissionParticipation($this->user, CompetitionCode::BusinessPlan);
    submissionRound($registration, 1, '+1 week', '+2 weeks');
    submissionRound($registration, 2, '-2 weeks', '-1 week');
    submissionRound($registration, 3, null, null);

    Livewire::test(SubmissionIndex::class)
        ->assertSee('Upcoming')
        ->assertSee('Closed')
        ->assertSee('Not scheduled');
});

test('an unverified registration shows a locked detail page', function () {
    $registration = submissionParticipation($this->user, CompetitionCode::BusinessCase, RegistrationStatus::Pending);
    $round = submissionRound($registration);

    Livewire::test(SubmissionDetail::class, ['competition' => 'bcc', 'stage' => (string) $round->round_order])
        ->assertSee('Submission locked')
        ->assertDontSee('Upload your file');
});

test('an upcoming stage hides the uploader until it opens', function () {
    $registration = submissionParticipation($this->user, CompetitionCode::BusinessPlan);
    $round = submissionRound($registration, 1, '+1 week', '+2 weeks');

    Livewire::test(SubmissionDetail::class, ['competition' => 'bpc', 'stage' => (string) $round->round_order])
        ->assertSee('Submission opens soon')
        ->assertDontSee('Upload your file');
});

test('an unscheduled stage hides the uploader', function () {
    $registration = submissionParticipation($this->user, CompetitionCode::BusinessPlan);
    $round = submissionRound($registration, 1, null, null);

    Livewire::test(SubmissionDetail::class, ['competition' => 'bpc', 'stage' => (string) $round->round_order])
        ->assertSee('Submission not scheduled')
        ->assertDontSee('Upload your file');
});

test('a closed stage keeps the submitted file visible without the uploader', function () {
    $registration = submissionParticipation($this->user, CompetitionCode::BusinessPlan);
    $round = submissionRound($registration, 1, '-2 weeks', '-1 week');
    Submission::factory()->create([
        'registration_id' => $registration->id,
        'competition_round_id' => $round->id,
        'public_id' => "catalyst/submissions/{$registration->id}/{$round->id}/deck.pdf",
    ]);

    Livewire::test(SubmissionDetail::class, ['competition' => 'bpc', 'stage' => (string) $round->round_order])
        ->assertSee('Submission closed')
        ->assertSee('deck.pdf')
        ->assertDontSee('Upload your file');
});

test('confirming an upload through the component stores the submission', function () {
    $registration = submissionParticipation($this->user, CompetitionCode::BusinessPlan);
    $round = submissionRound($registration);
    $file = new SubmissionFile(
        "catalyst/submissions/{$registration->id}/{$round->id}/deck.pdf", 'pdf', 1_000_000, 'https://x/y.pdf', null,
    );
    $this->inspector->files[$file->publicId] = $file;

    Livewire::test(SubmissionDetail::class, ['competition' => 'bpc', 'stage' => (string) $round->round_order])
        ->call('confirmUpload', $file->publicId)
        ->assertSee('Submission received')
        ->assertSee('version 1');

    expect(Submission::query()->count())->toBe(1);
});

test('overview and verified registration submission calls to action now resolve', function () {
    $submissionRoute = route('dashboard.submission.show', ['competition' => 'mcc', 'stage' => 'stage-1']);

    Livewire::test(Overview::class)
        ->set('scenario', 'active_participant')
        ->assertSee($submissionRoute);

    Livewire::test(RegistrationDetail::class, ['competition' => 'bpc'])
        ->set('scenario', 'verified')
        ->assertSee(route('dashboard.submission.show', ['competition' => 'bpc', 'stage' => 'stage-1']));
});
