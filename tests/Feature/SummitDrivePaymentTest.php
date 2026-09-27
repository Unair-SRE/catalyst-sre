<?php

use App\Actions\Summit\ConfirmSummitDrivePayment;
use App\Actions\Summit\CreateSummitOrder;
use App\Actions\Summit\RejectSummitOrder;
use App\Actions\Summit\ReplaceSummitDrivePayment;
use App\Enums\SummitOrderStatus;
use App\Models\PaymentSetting;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    PaymentSetting::query()->create([
        'contact_person_name' => 'Catalyst Contact',
        'contact_person_whatsapp' => '081200000000',
        'summit_ticket_price' => '100000.00',
        'is_active' => true,
    ]);
});

test('a summit drive folder is private to the buyer and admins', function () {
    $buyer = User::factory()->create();
    $order = app(CreateSummitOrder::class)->handle($buyer, ['Alya']);
    app(ConfirmSummitDrivePayment::class)->handle(
        $buyer,
        $order,
        'Sender',
        'https://drive.google.com/drive/folders/private-summit-payment',
        true,
    );
    $url = route('dashboard.summit-order.documents', $order);

    $this->actingAs($buyer)->get($url)
        ->assertRedirect('https://drive.google.com/drive/folders/private-summit-payment');

    $this->actingAs(User::factory()->admin()->create())->get($url)
        ->assertRedirect('https://drive.google.com/drive/folders/private-summit-payment');

    $this->actingAs(User::factory()->create())->get($url)->assertForbidden();
});

test('another user cannot submit the payment folder for an order', function () {
    $buyer = User::factory()->create();
    $order = app(CreateSummitOrder::class)->handle($buyer, ['Alya']);

    app(ConfirmSummitDrivePayment::class)->handle(
        User::factory()->create(),
        $order,
        'Sender',
        'https://drive.google.com/drive/folders/not-yours',
        true,
    );
})->throws(AuthorizationException::class);

test('a participant cannot replace a rejected folder but an admin can', function () {
    $buyer = User::factory()->create();
    $admin = User::factory()->admin()->create();
    $order = app(CreateSummitOrder::class)->handle($buyer, ['Alya']);
    app(ConfirmSummitDrivePayment::class)->handle(
        $buyer,
        $order,
        'Sender',
        'https://drive.google.com/drive/folders/original-proof',
        true,
    );
    app(RejectSummitOrder::class)->handle($admin, $order, 'The transfer amount is unclear.');

    expect($order->fresh()->review_note)->toBe('The transfer amount is unclear.');

    try {
        app(ReplaceSummitDrivePayment::class)->handle(
            $buyer,
            $order,
            'https://drive.google.com/drive/folders/corrected-proof',
        );
        $this->fail('A participant must not replace a rejected payment folder.');
    } catch (AuthorizationException) {
        // Expected.
    }

    $result = app(ReplaceSummitDrivePayment::class)->handle(
        $admin,
        $order,
        'https://drive.google.com/drive/folders/corrected-proof',
    );

    expect($result->payment_status)->toBe(SummitOrderStatus::WaitingVerification)
        ->and($result->payment_drive_url)->toBe('https://drive.google.com/drive/folders/corrected-proof')
        ->and($result->review_note)->toBeNull();
});

test('a summit payment folder can only be submitted once', function () {
    $buyer = User::factory()->create();
    $order = app(CreateSummitOrder::class)->handle($buyer, ['Alya']);
    $action = app(ConfirmSummitDrivePayment::class);

    $action->handle($buyer, $order, 'Sender', 'https://drive.google.com/drive/folders/first-proof', true);
    $action->handle($buyer, $order, 'Sender', 'https://drive.google.com/drive/folders/second-proof', true);
})->throws(ValidationException::class);
