<?php

use App\Livewire\Dashboard\Overview;
use Livewire\Livewire;

test('the dashboard overview route renders', function () {
    $this->get(route('dashboard.index'))
        ->assertOk()
        ->assertSee('Your Catalyst workspace');
});

test('the first-time state renders dashboard empty states without actions', function () {
    Livewire::test(Overview::class)
        ->assertSee('No competitions yet')
        ->assertSee('Join the Catalyst Summit experience with Talkshow and Exhibition access.')
        ->assertDontSee('Action Required');
});

test('the active participant state renders a competition and action', function () {
    Livewire::test(Overview::class)
        ->set('scenario', 'active_participant')
        ->assertSee('Mini Case Competition')
        ->assertSee('Complete your BPC registration')
        ->assertSee('Submit your MCC Stage 1 entry');
});

test('the revision and payment states render their required actions', function (string $scenario, string $action) {
    Livewire::test(Overview::class)
        ->set('scenario', $scenario)
        ->assertSee($action);
})->with([
    'registration revision' => ['revision_required', 'Update your MCC registration'],
    'competition payment' => ['payment_required', 'Complete your BCC registration payment'],
    'summit pass payment' => ['payment_required', 'Complete your Summit Pass payment'],
]);
