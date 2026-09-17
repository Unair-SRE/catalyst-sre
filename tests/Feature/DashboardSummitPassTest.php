<?php

use App\Livewire\Dashboard\Overview;
use App\Livewire\Dashboard\SummitPass;
use Livewire\Livewire;

test('summit pass route renders and marks its navigation active', function () {
    $this->get(route('dashboard.summit-pass.index'))
        ->assertOk()
        ->assertSee('Your access to Catalyst Summit Talkshow and Exhibition')
        ->assertSee('aria-current="page"', false);
});

test('no-pass state presents the purchase call to action and one-pass rule', function () {
    Livewire::test(SummitPass::class)
        ->assertSee('Experience Catalyst Summit')
        ->assertSee('Get Summit Pass')
        ->assertSee('Each account can purchase one non-transferable pass');
});

test('purchase state renders attendee and payment flow', function () {
    Livewire::test(SummitPass::class)
        ->set('scenario', 'purchase')
        ->assertSee('Attendee Information')
        ->assertSee('Summit Pass Information')
        ->assertSee('QRIS Payment Placeholder')
        ->assertSee('Submit Purchase');
});

test('summit pass review states render their intended outcomes', function (string $scenario, string $expected, string $notExpected = '') {
    $component = Livewire::test(SummitPass::class)
        ->set('scenario', $scenario)
        ->assertSee($expected);

    if ($notExpected !== '') {
        $component->assertDontSee($notExpected);
    }
})->with([
    'payment waiting without ticket QR' => ['payment_waiting', 'Payment under review', 'Ticket QR Placeholder'],
    'rejected with reason' => ['rejected', 'The uploaded payment proof could not be verified.', 'Your Summit Pass is ready'],
    'verified ticket' => ['verified', 'Ticket QR Placeholder'],
    'checked-in ticket' => ['checked_in', 'You’re checked in for Catalyst Summit'],
]);

test('purchase submission changes only the component-local prototype state', function () {
    Livewire::test(SummitPass::class)
        ->set('scenario', 'purchase')
        ->call('selectProof', [
            'name' => 'payment.pdf',
            'type' => 'application/pdf',
            'size_bytes' => 512_000,
        ])
        ->call('openConfirmation')
        ->assertSet('showConfirm', true)
        ->call('submitPurchase')
        ->assertSet('status', 'WAITING_VERIFICATION')
        ->assertSee('Payment under review');
});

test('overview summit pass calls to action resolve to the real route', function () {
    Livewire::test(Overview::class)
        ->assertSee(route('dashboard.summit-pass.index', ['scenario' => 'no_pass']))
        ->set('scenario', 'active_participant')
        ->assertSee(route('dashboard.summit-pass.index', ['scenario' => 'verified']));
});
