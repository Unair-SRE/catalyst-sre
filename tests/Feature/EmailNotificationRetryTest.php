<?php

use App\Actions\Email\RetryEmailNotification;
use App\Actions\Summit\ConfirmSummitDrivePayment;
use App\Actions\Summit\CreateSummitOrder;
use App\Actions\Summit\VerifySummitOrder;
use App\Enums\EmailStatus;
use App\Enums\UserRole;
use App\Filament\Resources\EmailNotifications\EmailNotificationResource;
use App\Jobs\SendSummitOrderEmail;
use App\Models\EmailNotification;
use App\Models\PaymentSetting;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    PaymentSetting::query()->create([
        'contact_person_name' => 'Catalyst Contact',
        'contact_person_whatsapp' => '081200000000',
        'summit_ticket_price' => '150000.00',
        'is_active' => true,
    ]);
});

function failedSummitEmail(User $buyer, User $admin): EmailNotification
{
    $order = app(CreateSummitOrder::class)->handle($buyer, ['Alya']);
    app(ConfirmSummitDrivePayment::class)->handle(
        $buyer,
        $order,
        'Buyer Sender',
        'https://drive.google.com/drive/folders/summit-payment-proof',
        true,
    );
    app(VerifySummitOrder::class)->handle($admin, $order);

    $log = EmailNotification::query()->firstOrFail();
    $log->markFailed('SMTP timeout');

    return $log->fresh();
}

function emailRetryAdmin(): User
{
    return User::factory()->create(['email_verified_at' => now(), 'role' => UserRole::Admin]);
}

test('an admin can retry a failed notification and it returns to pending with a new job', function () {
    Queue::fake();
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $log = failedSummitEmail($buyer, emailRetryAdmin());

    $result = app(RetryEmailNotification::class)->handle(emailRetryAdmin(), $log);

    expect($result->status)->toBe(EmailStatus::Pending)
        ->and($result->sent_at)->toBeNull()
        ->and($result->error_message)->toBeNull();

    Queue::assertPushed(SendSummitOrderEmail::class, function ($job) use ($log): bool {
        return $job->notificationId === $log->id;
    });
});

test('a non-failed notification cannot be retried', function () {
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $log = failedSummitEmail($buyer, emailRetryAdmin());
    $log->markSent();

    app(RetryEmailNotification::class)->handle(emailRetryAdmin(), $log);
})->throws(ValidationException::class);

test('a non-admin cannot retry a notification', function () {
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $log = failedSummitEmail($buyer, emailRetryAdmin());

    app(RetryEmailNotification::class)->handle($buyer, $log);
})->throws(AuthorizationException::class);

test('administrators can browse the email log in filament', function () {
    $admin = emailRetryAdmin();
    $participant = User::factory()->create();

    $this->actingAs($admin)
        ->get(EmailNotificationResource::getUrl('index'))
        ->assertOk();

    $this->actingAs($participant)
        ->get(EmailNotificationResource::getUrl('index'))
        ->assertForbidden();
});
