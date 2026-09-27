<?php

use App\Enums\SummitOrderStatus;
use App\Enums\SummitTicketStatus;
use App\Livewire\Dashboard\SummitPass;
use App\Models\PaymentSetting;
use App\Models\SummitOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->buyer = User::factory()->create(['name' => 'Alya Buyer']);
    $this->actingAs($this->buyer);

    PaymentSetting::query()->create([
        'contact_person_name' => 'Catalyst Contact',
        'contact_person_whatsapp' => '081200000000',
        'summit_ticket_price' => '150000.00',
        'is_active' => true,
    ]);
});

test('summit pass route renders the database-backed purchase form', function () {
    $this->get(route('dashboard.summit-pass.index'))
        ->assertOk()
        ->assertSee('Your access to Catalyst Summit Talkshow and Exhibition')
        ->assertSee('Google Drive proof')
        ->assertSee('aria-current="page"', false)
        ->assertDontSee('Prototype state');
});

test('buyer can submit an unlimited-size multi-ticket order with its own drive folder', function () {
    $holders = collect(range(1, 12))->map(fn (int $number): string => 'Holder '.$number)->all();

    Livewire::test(SummitPass::class)
        ->set('holderNames', $holders)
        ->set('senderName', 'Alya Sender')
        ->set('driveUrl', 'https://drive.google.com/drive/folders/summit-order-proof')
        ->set('documentsConfirmed', true)
        ->call('submitPurchase')
        ->assertHasNoErrors()
        ->assertSet('showPurchaseForm', false)
        ->assertSee('Payment under review')
        ->assertSee('12 ticket(s)');

    $order = SummitOrder::query()->with('tickets')->sole();

    expect($order->user_id)->toBe($this->buyer->id)
        ->and($order->quantity)->toBe(12)
        ->and($order->total_amount)->toBe('1800000.00')
        ->and($order->sender_name)->toBe('Alya Sender')
        ->and($order->payment_drive_url)->toBe('https://drive.google.com/drive/folders/summit-order-proof')
        ->and($order->payment_submitted_at)->not->toBeNull()
        ->and($order->payment_status)->toBe(SummitOrderStatus::WaitingVerification)
        ->and($order->tickets)->toHaveCount(12)
        ->and($order->tickets->pluck('status')->unique()->all())->toBe([SummitTicketStatus::WaitingVerification]);
});

test('summit payment requires a google drive folder rather than a file link', function () {
    Livewire::test(SummitPass::class)
        ->set('holderNames', ['Alya'])
        ->set('senderName', 'Alya Sender')
        ->set('driveUrl', 'https://drive.google.com/file/d/payment-proof/view')
        ->set('documentsConfirmed', true)
        ->call('submitPurchase')
        ->assertHasErrors('driveUrl');

    expect(SummitOrder::query()->count())->toBe(0);
});

test('buyer can create another independent order without belonging to a team', function () {
    Livewire::test(SummitPass::class)
        ->set('holderNames', ['First Holder'])
        ->set('senderName', 'First Sender')
        ->set('driveUrl', 'https://drive.google.com/drive/folders/first-order')
        ->set('documentsConfirmed', true)
        ->call('submitPurchase');

    Livewire::test(SummitPass::class)
        ->call('startPurchase')
        ->set('holderNames', ['Second Holder', 'Third Holder'])
        ->set('senderName', 'Second Sender')
        ->set('driveUrl', 'https://drive.google.com/drive/folders/second-order')
        ->set('documentsConfirmed', true)
        ->call('submitPurchase')
        ->assertHasNoErrors();

    expect(SummitOrder::query()->where('user_id', $this->buyer->id)->count())->toBe(2);
});

test('sales are disabled when summit pricing is not configured', function () {
    PaymentSetting::query()->update(['is_active' => false]);

    Livewire::test(SummitPass::class)
        ->assertSee('Summit ticket sales are not open');
});
