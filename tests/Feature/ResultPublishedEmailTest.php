<?php

use App\Actions\Results\PublishCompetitionRound;
use App\Actions\Results\SetQualificationResult;
use App\Enums\EmailStatus;
use App\Enums\QualificationStatus;
use App\Enums\RegistrationStatus;
use App\Enums\UserRole;
use App\Jobs\SendResultEmail;
use App\Mail\ResultPublishedMail;
use App\Models\Competition;
use App\Models\CompetitionRound;
use App\Models\EmailNotification;
use App\Models\Registration;
use App\Models\Team;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

function resultEmailAdmin(): User
{
    return User::factory()->create(['email_verified_at' => now(), 'role' => UserRole::Admin]);
}

function resultEmailFixture(User $admin, QualificationStatus $status = QualificationStatus::Qualified): array
{
    $team = Team::factory()->create();
    $registration = Registration::factory()
        ->for($team)
        ->for(Competition::factory())
        ->create(['status' => RegistrationStatus::Verified]);
    $round = CompetitionRound::factory()->for($registration->competition)->create([
        'name' => 'Penyisihan',
        'round_order' => 1,
    ]);

    app(SetQualificationResult::class)->handle($admin, $registration, $round, $status);

    return [$registration, $round];
}

test('publishing notifies every captain once', function () {
    Queue::fake();
    $admin = resultEmailAdmin();
    [$first, $round] = resultEmailFixture($admin);
    $secondTeam = Team::factory()->create();
    $second = Registration::factory()->for($secondTeam)->for($round->competition)->create([
        'status' => RegistrationStatus::Verified,
    ]);
    app(SetQualificationResult::class)->handle($admin, $second, $round, QualificationStatus::NotQualified);

    app(PublishCompetitionRound::class)->handle($admin, $round);

    expect(EmailNotification::query()->count())->toBe(2)
        ->and(EmailNotification::query()->where('status', EmailStatus::Pending)->count())->toBe(2);

    $recipients = EmailNotification::query()->pluck('recipient_email')->all();
    expect($recipients)->toContain($first->team->captain->email)
        ->and($recipients)->toContain($second->team->captain->email);

    Queue::assertPushed(SendResultEmail::class, 2);
});

test('republishing sends nothing new', function () {
    Queue::fake();
    $admin = resultEmailAdmin();
    [, $round] = resultEmailFixture($admin);

    app(PublishCompetitionRound::class)->handle($admin, $round);
    app(PublishCompetitionRound::class)->handle($admin, $round);

    expect(EmailNotification::query()->count())->toBe(1);
    Queue::assertPushed(SendResultEmail::class, 1);
});

test('the queued job sends the result mail and marks the log sent', function () {
    Mail::fake();
    $admin = resultEmailAdmin();
    [$registration, $round] = resultEmailFixture($admin);
    app(PublishCompetitionRound::class)->handle($admin, $round);
    $log = EmailNotification::query()->firstOrFail();

    $job = new SendResultEmail($log->id, $registration->qualificationResults()->firstOrFail()->id);
    $job->handle();

    Mail::assertSent(ResultPublishedMail::class, fn ($mail): bool => $mail->hasTo($registration->team->captain->email));
    expect($log->fresh()->status)->toBe(EmailStatus::Sent);
});

test('a failed log becomes sent after a successful retry run', function () {
    Mail::fake();
    $admin = resultEmailAdmin();
    [$registration, $round] = resultEmailFixture($admin);
    app(PublishCompetitionRound::class)->handle($admin, $round);
    $log = EmailNotification::query()->firstOrFail();
    $log->markFailed('SMTP timeout');

    (new SendResultEmail($log->id, $registration->qualificationResults()->firstOrFail()->id))->handle();

    expect($log->fresh()->status)->toBe(EmailStatus::Sent)
        ->and($log->fresh()->error_message)->toBeNull();
});
