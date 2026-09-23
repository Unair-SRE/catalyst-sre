<?php

use App\Actions\Summit\CreateSummitOrder;
use App\Enums\SummitOrderStatus;
use App\Enums\SummitTicketStatus;
use App\Models\PaymentSetting;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

function summitBuyer(): User
{
    return User::factory()->create(['email_verified_at' => now()]);
}

function openSummitSales(string $price = '150000.00'): void
{
    PaymentSetting::query()->create([
        'contact_person_name' => 'Catalyst Contact',
        'contact_person_whatsapp' => '081200000000',
        'summit_ticket_price' => $price,
        'is_active' => true,
    ]);
}

test('buying five tickets creates one order with five unique numeric codes', function () {
    $buyer = summitBuyer();
    openSummitSales();

    $order = app(CreateSummitOrder::class)->handle(
        $buyer,
        ['Alya', 'Bima', 'Citra', 'Dedi', 'Erika'],
    );

    expect($order->quantity)->toBe(5)
        ->and($order->total_amount)->toBe('750000.00')
        ->and($order->payment_status)->toBe(SummitOrderStatus::WaitingPayment)
        ->and($order->tickets)->toHaveCount(5)
        ->and($order->tickets->pluck('ticket_code')->unique())->toHaveCount(5);

    foreach ($order->tickets as $ticket) {
        expect($ticket->ticket_code)->toMatch('/^[0-9]{10}$/')
            ->and($ticket->status)->toBe(SummitTicketStatus::WaitingPayment);
    }
});

test('an unverified account cannot create a summit order', function () {
    openSummitSales();
    $buyer = User::factory()->create(['email_verified_at' => null]);

    app(CreateSummitOrder::class)->handle($buyer, ['Alya']);
})->throws(AuthorizationException::class);

test('an order cannot be created before the ticket price is configured', function () {
    $buyer = summitBuyer();

    app(CreateSummitOrder::class)->handle($buyer, ['Alya']);
})->throws(ValidationException::class, 'not open yet');
