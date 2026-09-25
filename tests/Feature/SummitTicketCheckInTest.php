<?php

use App\Actions\Summit\CheckInTicket;
use App\Actions\Summit\CreateSummitOrder;
use App\Actions\Summit\SubmitSummitPaymentProof;
use App\Actions\Summit\VerifySummitOrder;
use App\Contracts\SummitPaymentStorage;
use App\Enums\SummitTicketStatus;
use App\Enums\UserRole;
use App\Models\PaymentSetting;
use App\Models\SummitTicket;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Tests\Fakes\FakeSummitPaymentStorage;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->app->instance(SummitPaymentStorage::class, new FakeSummitPaymentStorage);

    PaymentSetting::query()->create([
        'contact_person_name' => 'Catalyst Contact',
        'contact_person_whatsapp' => '081200000000',
        'summit_ticket_price' => '150000.00',
        'is_active' => true,
    ]);
});

function activeSummitTicket(User $buyer): SummitTicket
{
    $order = app(CreateSummitOrder::class)->handle($buyer, ['Alya']);
    app(SubmitSummitPaymentProof::class)->handle(
        $buyer, $order, 'Sender', UploadedFile::fake()->create('proof.jpg', 100, 'image/jpeg'),
    );
    app(VerifySummitOrder::class)->handle(
        User::factory()->create(['email_verified_at' => now(), 'role' => UserRole::Admin]),
        $order,
    );

    return $order->tickets()->firstOrFail();
}

function checkInAdmin(): User
{
    return User::factory()->create(['email_verified_at' => now(), 'role' => UserRole::Admin]);
}

test('an admin can check in an active ticket exactly once', function () {
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $ticket = activeSummitTicket($buyer);

    $result = app(CheckInTicket::class)->handle(checkInAdmin(), $ticket);

    expect($result->status)->toBe(SummitTicketStatus::Used)
        ->and($result->checked_in_by)->not->toBeNull()
        ->and($result->checked_in_at)->not->toBeNull();
});

test('a second check-in is rejected without touching the first timestamp', function () {
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $ticket = activeSummitTicket($buyer);
    $admin = checkInAdmin();

    $first = app(CheckInTicket::class)->handle($admin, $ticket);

    try {
        app(CheckInTicket::class)->handle($admin, $ticket);
        $this->fail('A second check-in must be rejected.');
    } catch (ValidationException $exception) {
        expect($ticket->fresh()->checked_in_at->equalTo($first->checked_in_at))->toBeTrue();
    }
});

test('a ticket that is not active cannot be checked in', function () {
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $order = app(CreateSummitOrder::class)->handle($buyer, ['Alya']);
    $ticket = $order->tickets()->firstOrFail();

    app(CheckInTicket::class)->handle(checkInAdmin(), $ticket);
})->throws(ValidationException::class);

test('a non-admin cannot check in a ticket', function () {
    $buyer = User::factory()->create(['email_verified_at' => now()]);
    $ticket = activeSummitTicket($buyer);

    app(CheckInTicket::class)->handle($buyer, $ticket);
})->throws(AuthorizationException::class);
