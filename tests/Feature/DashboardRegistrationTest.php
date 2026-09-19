<?php

use App\Livewire\Dashboard\Overview;
use App\Livewire\Dashboard\RegistrationDetail;
use App\Livewire\Dashboard\RegistrationIndex;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('registration index route renders', function () {
    $this->get(route('dashboard.registration.index'))
        ->assertOk()
        ->assertSee('Manage your competition registrations and team information');
});

test('registration detail route renders for a valid slug', function () {
    $this->get(route('dashboard.registration.show', ['competition' => 'bpc']))
        ->assertOk()
        ->assertSee('Business Plan Competition');
});

test('registration detail route rejects an invalid slug', function () {
    $this->get('/dashboard/registration/invalid')->assertNotFound();
});

test('first-time registration index displays every competition', function () {
    Livewire::test(RegistrationIndex::class)
        ->assertSee('Mini Case Competition')
        ->assertSee('Business Case Competition')
        ->assertSee('Business Plan Competition')
        ->assertSee('Start Registration');
});

test('mixed registration index displays different registration states', function () {
    Livewire::test(RegistrationIndex::class)
        ->set('scenario', 'mixed_registration')
        ->assertSee('Verified')
        ->assertSee('Draft')
        ->assertSee('Start Registration');
});

test('draft registration detail is editable and keeps WhatsApp locked', function () {
    Livewire::test(RegistrationDetail::class, ['competition' => 'bpc'])
        ->assertSee('Submit Registration')
        ->assertSee('Available immediately after you submit your registration.')
        ->assertSee('Locked')
        ->assertDontSee('Join WhatsApp Group');
});

test('registration detail renders review, revision, verification, and rejection states', function (string $scenario, string $expected) {
    Livewire::test(RegistrationDetail::class, ['competition' => 'bpc'])
        ->set('scenario', $scenario)
        ->assertSee($expected);
})->with([
    'under review' => ['under_review', 'Registration under review'],
    'revision' => ['revision_required', 'We couldn’t access your registration folder.'],
    'verified' => ['verified', 'Registration verified'],
    'rejected' => ['rejected', 'Team members did not meet the eligibility requirements'],
    'payment waived' => ['payment_waived', 'Payment waived'],
]);

test('submitted registration provides WhatsApp access and keeps submission separate', function () {
    Livewire::test(RegistrationDetail::class, ['competition' => 'bpc'])
        ->set('scenario', 'submitted')
        ->assertSee('Join WhatsApp Group')
        ->assertSee('Locked');
});

test('under-review registration provides WhatsApp access while submission stays locked', function () {
    Livewire::test(RegistrationDetail::class, ['competition' => 'bpc'])
        ->set('scenario', 'under_review')
        ->assertSee('Join WhatsApp Group')
        ->assertSee('Locked');
});

test('draft submission transitions to submitted state in the component only', function () {
    Livewire::test(RegistrationDetail::class, ['competition' => 'bpc'])
        ->set('form.eligible', true)
        ->call('openConfirmation')
        ->assertSet('showConfirm', true)
        ->call('submitRegistration')
        ->assertSet('scenario', 'submitted')
        ->assertSee('Registration submitted');
});

test('overview registration actions point to registration detail routes', function () {
    Livewire::test(Overview::class)
        ->set('scenario', 'active_participant')
        ->assertSee(route('dashboard.registration.show', ['competition' => 'bpc']));
});
