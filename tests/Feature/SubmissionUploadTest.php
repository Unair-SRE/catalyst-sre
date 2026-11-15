<?php

use App\Actions\Submissions\ConfirmSubmissionUpload;
use App\Actions\Submissions\RequestSubmissionUpload;
use App\Contracts\SubmissionFile;
use App\Contracts\SubmissionFileInspector;
use App\Enums\CompetitionCode;
use App\Enums\RegistrationStatus;
use App\Models\Competition;
use App\Models\CompetitionRound;
use App\Models\Registration;
use App\Models\Submission;
use App\Models\Team;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\Fakes\FakeSubmissionInspector;

uses(RefreshDatabase::class);

beforeEach(function () {
    $inspector = new FakeSubmissionInspector;
    $this->app->instance(SubmissionFileInspector::class, $inspector);
    $this->inspector = $inspector;

    config()->set('services.cloudinary.cloud_name', 'demo');
    config()->set('services.cloudinary.upload_preset', 'catalyst-unsigned');
});

function verifiedSubmissionRegistration(): Registration
{
    static $index = 0;
    $codes = CompetitionCode::cases();

    $team = Team::factory()->create();
    $competition = Competition::factory()->create(['code' => $codes[$index++ % count($codes)]]);

    return Registration::factory()->for($team)->for($competition)->create([
        'status' => RegistrationStatus::Verified,
    ]);
}

function openSubmissionRound(Registration $registration, int $order = 1): CompetitionRound
{
    return CompetitionRound::factory()->for($registration->competition)->create([
        'name' => 'Stage '.$order,
        'round_order' => $order,
        'starts_at' => now()->subDay(),
        'ends_at' => now()->addWeek(),
    ]);
}

function storedSubmissionFile(Registration $registration, CompetitionRound $round, string $suffix = 'deck.pdf'): SubmissionFile
{
    return new SubmissionFile(
        publicId: "catalyst/submissions/{$registration->id}/{$round->id}/{$suffix}",
        folder: "catalyst/submissions/{$registration->id}/{$round->id}",
        format: 'pdf',
        bytes: 1_200_000,
        url: 'https://res.cloudinary.com/demo/raw/upload/v1/file.pdf',
        etag: 'abc123',
    );
}

test('a captain receives upload parameters scoped to their submission', function () {
    $registration = verifiedSubmissionRegistration();
    $round = openSubmissionRound($registration);

    $params = app(RequestSubmissionUpload::class)->handle(
        $registration->team->captain, $registration, $round,
    );

    expect($params['folder'])->toBe("catalyst/submissions/{$registration->id}/{$round->id}")
        ->and($params['max_bytes'])->toBe(5 * 1024 * 1024)
        ->and($params['allowed_formats'])->toBe(['pdf', 'ppt', 'pptx']);
});

test('upload parameters are refused for an unverified registration', function () {
    $registration = verifiedSubmissionRegistration();
    $round = openSubmissionRound($registration);

    $pending = verifiedSubmissionRegistration();
    $pending->update(['status' => RegistrationStatus::Pending]);

    app(RequestSubmissionUpload::class)->handle($pending->team->captain, $pending, openSubmissionRound($pending));
})->throws(ValidationException::class);

test('upload parameters are refused for a round of another competition', function () {
    $registration = verifiedSubmissionRegistration();
    $round = openSubmissionRound($registration);
    $otherCode = collect(CompetitionCode::cases())->first(fn ($code): bool => $code !== $registration->competition->code);
    $other = Competition::factory()->create(['code' => $otherCode]);
    $foreignRound = CompetitionRound::factory()->for($other)->create(['name' => 'X', 'round_order' => 1]);

    app(RequestSubmissionUpload::class)->handle($registration->team->captain, $registration, $foreignRound);
})->throws(ValidationException::class);

test('upload parameters are refused outside the stage window', function () {
    $registration = verifiedSubmissionRegistration();
    $round = CompetitionRound::factory()->for($registration->competition)->create([
        'name' => 'Late',
        'round_order' => 1,
        'starts_at' => now()->subWeeks(2),
        'ends_at' => now()->subWeek(),
    ]);

    app(RequestSubmissionUpload::class)->handle($registration->team->captain, $registration, $round);
})->throws(ValidationException::class);

test('confirming stores a versioned immutable submission record', function () {
    $registration = verifiedSubmissionRegistration();
    $round = openSubmissionRound($registration);
    $file = storedSubmissionFile($registration, $round);
    $this->inspector->files[$file->publicId] = $file;

    $submission = app(ConfirmSubmissionUpload::class)->handle(
        $registration->team->captain, $registration, $round, $file->publicId,
    );

    expect($submission->version)->toBe(1)
        ->and($submission->format)->toBe('pdf')
        ->and($submission->submitted_by)->toBe($registration->team->captain->id)
        ->and($submission->submitted_at)->not->toBeNull();
});

test('a resubmission bumps the version and keeps the new file', function () {
    $registration = verifiedSubmissionRegistration();
    $round = openSubmissionRound($registration);
    $first = storedSubmissionFile($registration, $round, 'v1.pdf');
    $second = storedSubmissionFile($registration, $round, 'v2.pdf');
    $this->inspector->files[$first->publicId] = $first;
    $this->inspector->files[$second->publicId] = $second;

    app(ConfirmSubmissionUpload::class)->handle($registration->team->captain, $registration, $round, $first->publicId);
    $resubmitted = app(ConfirmSubmissionUpload::class)->handle($registration->team->captain, $registration, $round, $second->publicId);

    expect($resubmitted->version)->toBe(2)
        ->and($resubmitted->public_id)->toBe($second->publicId)
        ->and(Submission::query()->count())->toBe(1);
});

test('confirming rejects a file that was never uploaded', function () {
    $registration = verifiedSubmissionRegistration();
    $round = openSubmissionRound($registration);

    app(ConfirmSubmissionUpload::class)->handle(
        $registration->team->captain, $registration, $round, "catalyst/submissions/{$registration->id}/{$round->id}/ghost.pdf",
    );
})->throws(ValidationException::class, 'could not be found');

test('confirming rejects a file from another folder', function () {
    $registration = verifiedSubmissionRegistration();
    $round = openSubmissionRound($registration);
    $foreign = new SubmissionFile(
        'catalyst/submissions/999/999/other.pdf', 'catalyst/submissions/999/999', 'pdf', 100, 'https://x/y.pdf', null,
    );
    test()->inspector->files[$foreign->publicId] = $foreign;

    app(ConfirmSubmissionUpload::class)->handle(
        $registration->team->captain, $registration, $round, $foreign->publicId,
    );
})->throws(ValidationException::class, 'does not belong');

test('confirming rejects a disallowed format', function () {
    $registration = verifiedSubmissionRegistration();
    $round = openSubmissionRound($registration);
    $file = new SubmissionFile(
        "catalyst/submissions/{$registration->id}/{$round->id}/run.exe",
        "catalyst/submissions/{$registration->id}/{$round->id}",
        'exe', 100, 'https://x/y.exe', null,
    );
    test()->inspector->files[$file->publicId] = $file;

    app(ConfirmSubmissionUpload::class)->handle(
        $registration->team->captain, $registration, $round, $file->publicId,
    );
})->throws(ValidationException::class, 'Only PDF');

test('confirming rejects an oversized file', function () {
    $registration = verifiedSubmissionRegistration();
    $round = openSubmissionRound($registration);
    $file = new SubmissionFile(
        "catalyst/submissions/{$registration->id}/{$round->id}/big.pdf",
        "catalyst/submissions/{$registration->id}/{$round->id}",
        'pdf', 6 * 1024 * 1024, 'https://x/y.pdf', null,
    );
    test()->inspector->files[$file->publicId] = $file;

    app(ConfirmSubmissionUpload::class)->handle(
        $registration->team->captain, $registration, $round, $file->publicId,
    );
})->throws(ValidationException::class, 'must not exceed');

test('a claimed public id cannot be reused by another submission', function () {
    $first = verifiedSubmissionRegistration();
    $round = openSubmissionRound($first);
    $file = storedSubmissionFile($first, $round);
    $this->inspector->files[$file->publicId] = $file;
    app(ConfirmSubmissionUpload::class)->handle($first->team->captain, $first, $round, $file->publicId);

    $second = verifiedSubmissionRegistration();
    $secondRound = openSubmissionRound($second);

    app(ConfirmSubmissionUpload::class)->handle($second->team->captain, $second, $secondRound, $file->publicId);
})->throws(ValidationException::class);

test('a non-captain cannot request or confirm a submission', function () {
    $registration = verifiedSubmissionRegistration();
    $round = openSubmissionRound($registration);
    $outsider = User::factory()->create(['email_verified_at' => now()]);

    app(RequestSubmissionUpload::class)->handle($outsider, $registration, $round);
})->throws(AuthorizationException::class);
