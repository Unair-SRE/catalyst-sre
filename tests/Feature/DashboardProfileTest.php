<?php

use App\Livewire\Dashboard\Profile;
use App\Models\User;
use App\Notifications\VerifyEmailOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create([
        'name' => 'Alya Pratama',
        'email' => 'alya@example.test',
        'whatsapp' => '081200000000',
        'password' => Hash::make('current-password'),
    ]);

    $this->actingAs($this->user);
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
        ->assertSet('profile.email', 'alya@example.test')
        ->assertSet('profile.whatsapp', '081200000000')
        ->assertSee('WhatsApp Number')
        ->assertSee('Institution / School / University')
        ->assertSee('Use your legal name. This name may be used for certificates and official Catalyst documents.');
});

test('profile name and whatsapp changes are saved to the authenticated user', function () {
    Livewire::test(Profile::class)
        ->set('profile.name', 'Alya Putri Pratama')
        ->set('profile.whatsapp', '081234567890')
        ->call('saveProfile')
        ->assertHasNoErrors()
        ->assertSet('originalProfile.name', 'Alya Putri Pratama')
        ->assertSet('originalProfile.whatsapp', '081234567890')
        ->assertSee('Profile updated');

    expect($this->user->refresh())
        ->name->toBe('Alya Putri Pratama')
        ->whatsapp->toBe('081234567890')
        ->email->toBe('alya@example.test')
        ->email_verified_at->not->toBeNull();
});

test('changing profile email requires a new otp verification', function () {
    Notification::fake();

    Livewire::test(Profile::class)
        ->set('profile.email', 'alya.changed@example.test')
        ->call('saveProfile')
        ->assertHasNoErrors()
        ->assertSee('Profile updated');

    $this->user->refresh();

    expect($this->user)
        ->email->toBe('alya.changed@example.test')
        ->email_verified_at->toBeNull()
        ->otp_hash->not->toBeNull()
        ->otp_expires_at->not->toBeNull();

    Notification::assertSentTo($this->user, VerifyEmailOtp::class);
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

test('password update requires the current password', function () {
    Livewire::test(Profile::class)
        ->set('password.current', 'wrong-password')
        ->set('password.new', 'new-password')
        ->set('password.confirmation', 'new-password')
        ->call('updatePassword')
        ->assertHasErrors(['password.current']);
});

test('password update stores a new password hash and clears fields', function () {
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

    expect(Hash::check('new-password', $this->user->refresh()->password))->toBeTrue();
});
