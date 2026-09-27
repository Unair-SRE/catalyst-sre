<?php

use App\Actions\Summit\CheckInTicket;
use App\Actions\Summit\ConfirmSummitDrivePayment;
use App\Actions\Summit\CreateSummitOrder;
use App\Actions\Summit\VerifySummitOrder;
use App\Enums\SummitTicketStatus;
use App\Models\PaymentSetting;
use App\Models\SummitTicket;
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

function activeDriveSummitTicket(User $buyer): SummitTicket
{
    $order = app(CreateSummitOrder::class)->handle($buyer, ['Alya']);
    app(ConfirmSummitDrivePayment::class)->handle(
        $buyer,
        $order,
        'Sender',
        'https://drive.google.com/drive/folders/check-in-payment',
        true,
    );
    app(VerifySummitOrder::class)->handle(User::factory()->admin()->create(), $order);

    return $order->tickets()->firstOrFail();
}

test('an admin can check in an active ticket exactly once', function () {
    $ticket = activeDriveSummitTicket(User::factory()->create());

    $result = app(CheckInTicket::class)->handle(User::factory()->admin()->create(), $ticket);

    expect($result->status)->toBe(SummitTicketStatus::Used)
        ->and($result->checked_in_by)->not->toBeNull()
        ->and($result->checked_in_at)->not->toBeNull();
});

test('a second check-in is rejected without changing its timestamp', function () {
    $ticket = activeDriveSummitTicket(User::factory()->create());
    $admin = User::factory()->admin()->create();
    $first = app(CheckInTicket::class)->handle($admin, $ticket);

    try {
        app(CheckInTicket::class)->handle($admin, $ticket);
        $this->fail('A second check-in must be rejected.');
    } catch (ValidationException) {
        expect($ticket->fresh()->checked_in_at->equalTo($first->checked_in_at))->toBeTrue();
    }
});

test('a non-admin cannot check in a ticket', function () {
    $buyer = User::factory()->create();
    $ticket = activeDriveSummitTicket($buyer);

    app(CheckInTicket::class)->handle($buyer, $ticket);
})->throws(AuthorizationException::class);
