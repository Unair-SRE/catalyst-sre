<?php

use App\Actions\Summit\ConfirmSummitDrivePayment;
use App\Actions\Summit\CreateSummitOrder;
use App\Actions\Summit\VerifySummitOrder;
use App\Enums\EmailStatus;
use App\Enums\UserRole;
use App\Jobs\SendSummitOrderEmail;
use App\Mail\SummitOrderApprovedMail;
use App\Models\EmailNotification;
use App\Models\PaymentSetting;
use App\Models\SummitOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

uses(RefreshDatabase::class);

beforeEach(function () {
    PaymentSetting::query()->create([
        'contact_person_name' => 'Catalyst Contact',
        'contact_person_whatsapp' => '081200000000',
        'summit_ticket_price' => '150000.00',
        'is_active' => true,
    ]);
});

function emailSummitOrder(User $buyer): SummitOrder
{
    $order = app(CreateSummitOrder::class)->handle($buyer, ['Alya', 'Bima']);

    return app(ConfirmSummitDrivePayment::class)->handle(
        $buyer,
        $order,
        'Buyer Sender',
        'https://drive.google.com/drive/folders/summit-payment-proof',
        true,
    );
}

function emailVerifyAdmin(): User
{
    return User::factory()->create(['email_verified_at' => now(), 'role' => UserRole::Admin]);
}

test('approving an order queues one ticket email and logs it as pending', function () {
    Queue::fake();
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $order = emailSummitOrder($buyer);

    app(VerifySummitOrder::class)->handle(emailVerifyAdmin(), $order);

    expect(EmailNotification::query()->count())->toBe(1);

    $log = EmailNotification::query()->firstOrFail();
    expect($log->status)->toBe(EmailStatus::Pending)
        ->and($log->recipient_email)->toBe($buyer->email)
        ->and($log->type)->toBe('SUMMIT_ORDER_APPROVED');

    Queue::assertPushed(SendSummitOrderEmail::class, function ($job) use ($log, $order): bool {
        return $job->notificationId === $log->id && $job->orderId === $order->id;
    });
});

test('a repeated approval sends no additional email', function () {
    Queue::fake();
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $order = emailSummitOrder($buyer);
    $admin = emailVerifyAdmin();

    app(VerifySummitOrder::class)->handle($admin, $order);
    app(VerifySummitOrder::class)->handle($admin, $order);

    expect(EmailNotification::query()->count())->toBe(1);
    Queue::assertPushed(SendSummitOrderEmail::class, 1);
});

test('the queued job sends the mail and marks the log sent', function () {
    Mail::fake();
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $order = emailSummitOrder($buyer);
    $verified = app(VerifySummitOrder::class)->handle(emailVerifyAdmin(), $order);
    $log = EmailNotification::query()->firstOrFail();

    (new SendSummitOrderEmail($log->id, $verified->id))->handle();

    Mail::assertSent(SummitOrderApprovedMail::class, fn ($mail): bool => $mail->hasTo($buyer->email));
    expect($log->fresh()->status)->toBe(EmailStatus::Sent)
        ->and($log->fresh()->sent_at)->not->toBeNull();
});
