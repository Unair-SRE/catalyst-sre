<?php

use App\Livewire\Dashboard\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('profile route renders and marks its navigation active', function () {
    $this->get(route('dashboard.profile.index'))
        ->assertOk()
        ->assertSee('Manage your personal information and account security')
        ->assertSee('aria-current="page"', false);
});

test('profile fields and legal-name guidance render', function () {
    Livewire::test(Profile::class)
        ->assertSet('profile.name', 'Alya Pratama')
        ->assertSee('WhatsApp Number')
        ->assertSee('Institution / School / University')
        ->assertSee('Use your legal name. This name may be used for certificates and official Catalyst documents.');
});

test('profile changes can be saved in component-local state', function () {
    Livewire::test(Profile::class)
        ->set('profile.name', 'Alya Putri Pratama')
        ->call('saveProfile')
        ->assertHasNoErrors()
        ->assertSet('originalProfile.name', 'Alya Putri Pratama')
        ->assertSee('Profile updated');
});

test('profile requires a valid email address', function () {
    Livewire::test(Profile::class)
        ->set('profile.email', 'not-an-email')
        ->call('saveProfile')
        ->assertHasErrors(['profile.email' => 'email']);
});

test('password confirmation must match', function () {
    Livewire::test(Profile::class)
        ->set('password.current', 'current-password')
        ->set('password.new', 'new-password')
        ->set('password.confirmation', 'different-password')
        ->call('updatePassword')
        ->assertHasErrors(['password.new' => 'same']);
});

test('password prototype update clears fields and reports success', function () {
    Livewire::test(Profile::class)
        ->set('password.current', 'current-password')
        ->set('password.new', 'new-password')
        ->set('password.confirmation', 'new-password')
        ->call('updatePassword')
        ->assertHasNoErrors()
        ->assertSet('password.current', '')
        ->assertSet('password.new', '')
        ->assertSet('password.confirmation', '')
        ->assertSee('Password updated');
});
