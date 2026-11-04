<?php

use App\Enums\EmailStatus;
use App\Models\EmailNotification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('an email notification starts as pending and can transition to sent or failed', function () {
    $user = User::factory()->create();

    $notification = EmailNotification::query()->create([
        'user_id' => $user->id,
        'recipient_email' => $user->email,
        'type' => 'TICKET',
        'status' => EmailStatus::Pending,
    ]);

    expect($notification->status)->toBe(EmailStatus::Pending)
        ->and($notification->sent_at)->toBeNull();

    $notification->markSent();

    expect($notification->fresh()->status)->toBe(EmailStatus::Sent)
        ->and($notification->fresh()->sent_at)->not->toBeNull();

    $notification->markFailed('SMTP timeout');

    expect($notification->fresh()->status)->toBe(EmailStatus::Failed)
        ->and($notification->fresh()->error_message)->toBe('SMTP timeout');
});

test('system notifications can exist without a user', function () {
    $notification = EmailNotification::query()->create([
        'user_id' => null,
        'recipient_email' => 'ops@example.com',
        'type' => 'RESULT',
        'status' => EmailStatus::Pending,
    ]);

    expect($notification->user)->toBeNull()
        ->and($notification->status)->toBe(EmailStatus::Pending);
});
