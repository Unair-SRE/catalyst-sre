<?php

use App\Models\User;
use App\Notifications\VerifyEmailOtp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

test('registration sends a six digit otp and stores only its hash', function () {
    Notification::fake();

    $this->post('/register', [
        'name' => 'Eka Developer',
        'email' => 'eka@example.com',
        'whatsapp' => '081234567890',
        'password' => 'secure-password',
        'password_confirmation' => 'secure-password',
    ])->assertRedirect('/dashboard');

    $user = User::query()->where('email', 'eka@example.com')->firstOrFail();
    $user->refresh();

    Notification::assertSentTo($user, VerifyEmailOtp::class, function (VerifyEmailOtp $notification) use ($user) {
        return preg_match('/^\d{6}$/', $notification->code) === 1
            && $user->otp_hash !== $notification->code
            && Hash::check($notification->code, $user->otp_hash);
    });

    expect($user->otp_expires_at->isBetween(now()->addMinutes(4)->addSeconds(59), now()->addMinutes(5)->addSecond()))->toBeTrue();
});

test('a valid otp verifies the email and can only be used once', function () {
    Notification::fake();
    $user = User::factory()->unverified()->create();
    $user->sendEmailVerificationNotification();

    $code = null;
    Notification::assertSentTo($user, VerifyEmailOtp::class, function (VerifyEmailOtp $notification) use (&$code) {
        $code = $notification->code;

        return true;
    });

    $this->actingAs($user)
        ->post('/email/verify-otp', ['otp' => $code])
        ->assertRedirect('/dashboard');

    expect($user->refresh()->hasVerifiedEmail())->toBeTrue()
        ->and($user->otp_hash)->toBeNull()
        ->and($user->otp_expires_at)->toBeNull();

    $this->post('/email/verify-otp', ['otp' => $code])
        ->assertSessionHasErrors('otp');
});

test('an invalid or expired otp is rejected', function () {
    Notification::fake();
    $user = User::factory()->unverified()->create();
    $user->sendEmailVerificationNotification();

    $this->actingAs($user)
        ->post('/email/verify-otp', ['otp' => '000000'])
        ->assertSessionHasErrors('otp');

    $user->forceFill(['otp_expires_at' => now()->subSecond()])->save();

    $this->post('/email/verify-otp', ['otp' => '123456'])
        ->assertSessionHasErrors('otp');

    expect($user->refresh()->hasVerifiedEmail())->toBeFalse();
});

test('otp resend requires a sixty second cooldown and invalidates the previous code', function () {
    Notification::fake();
    $user = User::factory()->unverified()->create();
    $user->sendEmailVerificationNotification();
    $originalHash = $user->refresh()->otp_hash;

    $this->actingAs($user)
        ->post('/email/verification-notification')
        ->assertSessionHasErrors('otp');

    $this->travel(61)->seconds();

    $this->post('/email/verification-notification')
        ->assertSessionHasNoErrors();

    expect($user->refresh()->otp_hash)->not->toBe($originalHash);
    Notification::assertSentToTimes($user, VerifyEmailOtp::class, 2);
});

test('private dashboard routes require authentication and verified email', function () {
    $this->get('/dashboard')->assertRedirect('/login');

    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect('/email/verify');

    $user->markEmailAsVerified();

    $this->get('/dashboard')->assertOk();
});
