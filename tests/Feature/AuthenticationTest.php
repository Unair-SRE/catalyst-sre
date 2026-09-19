<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;

uses(RefreshDatabase::class);

test('a participant can register with the required account data', function () {
    $response = $this->post('/register', [
        'name' => ' Eka Developer ',
        'email' => 'EKA@EXAMPLE.COM',
        'whatsapp' => '081234567890',
        'password' => 'secure-password',
        'password_confirmation' => 'secure-password',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();

    $user = User::query()->where('email', 'eka@example.com')->firstOrFail();

    expect($user->name)->toBe('Eka Developer')
        ->and($user->whatsapp)->toBe('081234567890')
        ->and($user->role)->toBe(UserRole::Participant)
        ->and($user->email_verified_at)->toBeNull()
        ->and(Hash::check('secure-password', $user->password))->toBeTrue();
});

test('registration requires a unique email and whatsapp number', function () {
    User::factory()->create(['email' => 'used@example.com']);

    $this->post('/register', [
        'name' => 'Eka Developer',
        'email' => 'used@example.com',
        'password' => 'secure-password',
        'password_confirmation' => 'secure-password',
    ])->assertSessionHasErrors(['email', 'whatsapp']);

    $this->assertGuest();
});

test('a user can log in and log out', function () {
    $user = User::factory()->create([
        'email' => 'eka@example.com',
        'password' => 'secure-password',
    ]);

    $this->post('/login', [
        'email' => 'EKA@example.com',
        'password' => 'secure-password',
    ])->assertRedirect('/dashboard');

    $this->assertAuthenticatedAs($user);

    $this->post('/logout')->assertRedirect('/');
    $this->assertGuest();
});

test('login attempts are rate limited', function () {
    User::factory()->create([
        'email' => 'eka@example.com',
        'password' => 'correct-password',
    ]);

    foreach (range(1, 5) as $attempt) {
        $this->from('/login')->post('/login', [
            'email' => 'eka@example.com',
            'password' => 'wrong-password',
        ])->assertSessionHasErrors('email');
    }

    $this->from('/login')->post('/login', [
        'email' => 'eka@example.com',
        'password' => 'wrong-password',
    ])->assertTooManyRequests();
});

test('a password reset link can be requested', function () {
    Notification::fake();
    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email])
        ->assertSessionHasNoErrors();

    Notification::assertSentTo($user, ResetPassword::class);
});

test('a password can be reset with a valid token', function () {
    $user = User::factory()->create(['password' => 'old-password']);
    $token = Password::broker()->createToken($user);

    $this->post('/reset-password', [
        'token' => $token,
        'email' => $user->email,
        'password' => 'new-secure-password',
        'password_confirmation' => 'new-secure-password',
    ])->assertSessionHasNoErrors();

    expect(Hash::check('new-secure-password', $user->refresh()->password))->toBeTrue();
});
