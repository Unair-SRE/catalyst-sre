<?php

use App\Actions\Summit\ConfirmSummitDrivePayment;
use App\Actions\Summit\CreateSummitOrder;
use App\Actions\Summit\RejectSummitOrder;
use App\Actions\Summit\VerifySummitOrder;
use App\Enums\SummitOrderStatus;
use App\Enums\SummitTicketStatus;
use App\Models\PaymentSetting;
use App\Models\SummitOrder;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
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

function driveSummitOrder(User $buyer): SummitOrder
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

test('a buyer submits one drive folder and the order moves to verification', function () {
    $buyer = User::factory()->create();
    $result = driveSummitOrder($buyer);

    expect($result->payment_status)->toBe(SummitOrderStatus::WaitingVerification)
        ->and($result->sender_name)->toBe('Buyer Sender')
        ->and($result->hasPaymentFolder())->toBeTrue()
        ->and($result->tickets->pluck('status')->unique()->all())
        ->toBe([SummitTicketStatus::WaitingVerification]);
});

test('an admin approval activates tickets without external file storage', function () {
    $buyer = User::factory()->create();
    $order = driveSummitOrder($buyer);
    $admin = User::factory()->admin()->create();

    $result = app(VerifySummitOrder::class)->handle($admin, $order);

    expect($result->payment_status)->toBe(SummitOrderStatus::Verified)
        ->and($result->verified_by)->toBe($admin->id)
        ->and($result->verified_at)->not->toBeNull()
        ->and($result->tickets->pluck('status')->unique()->all())->toBe([SummitTicketStatus::Active]);

    foreach ($result->tickets as $ticket) {
        expect($ticket->pdf_url)->toBeNull()
            ->and($ticket->pdf_file_id)->toBeNull();
    }
});

test('a repeated approval is a harmless no-op', function () {
    $buyer = User::factory()->create();
    $order = driveSummitOrder($buyer);
    $admin = User::factory()->admin()->create();

    $first = app(VerifySummitOrder::class)->handle($admin, $order);
    $second = app(VerifySummitOrder::class)->handle($admin, $order);

    expect($second->id)->toBe($first->id)
        ->and($second->payment_status)->toBe(SummitOrderStatus::Verified)
        ->and($order->tickets()->count())->toBe(2);
});

test('an order without a drive payment folder cannot be approved', function () {
    $buyer = User::factory()->create();
    $order = app(CreateSummitOrder::class)->handle($buyer, ['Alya']);

    app(VerifySummitOrder::class)->handle(User::factory()->admin()->create(), $order);
})->throws(ValidationException::class);

test('a rejection closes the order and every ticket', function () {
    $buyer = User::factory()->create();
    $order = driveSummitOrder($buyer);

    $result = app(RejectSummitOrder::class)->handle(
        User::factory()->admin()->create(),
        $order,
        'The Drive folder cannot be opened.',
    );

    expect($result->payment_status)->toBe(SummitOrderStatus::Rejected)
        ->and($result->review_note)->toBe('The Drive folder cannot be opened.')
        ->and($result->tickets->pluck('status')->unique()->all())->toBe([SummitTicketStatus::Rejected]);
});

test('a non-owner cannot submit a drive folder for another user order', function () {
    $buyer = User::factory()->create();
    $order = app(CreateSummitOrder::class)->handle($buyer, ['Alya']);

    app(ConfirmSummitDrivePayment::class)->handle(
        User::factory()->create(),
        $order,
        'Sender',
        'https://drive.google.com/drive/folders/not-my-order',
        true,
    );
})->throws(AuthorizationException::class);
