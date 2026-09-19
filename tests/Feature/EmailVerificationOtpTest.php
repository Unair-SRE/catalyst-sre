<?php

use App\Models\EmailVerificationOtp;
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
    $otp = $user->emailVerificationOtp()->firstOrFail();

    Notification::assertSentTo($user, VerifyEmailOtp::class, function (VerifyEmailOtp $notification) use ($otp) {
        return preg_match('/^\d{6}$/', $notification->code) === 1
            && $otp->otp_hash !== $notification->code
            && Hash::check($notification->code, $otp->otp_hash);
    });

    expect($otp->last_sent_at->diffInSeconds($otp->expires_at))->toBe(300.0);
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
        ->and($user->emailVerificationOtp->consumed_at)->not->toBeNull();

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

    EmailVerificationOtp::query()->whereBelongsTo($user)->update([
        'expires_at' => now()->subSecond(),
    ]);

    $this->post('/email/verify-otp', ['otp' => '123456'])
        ->assertSessionHasErrors('otp');

    expect($user->refresh()->hasVerifiedEmail())->toBeFalse();
});

test('otp resend requires a sixty second cooldown and invalidates the previous code', function () {
    Notification::fake();
    $user = User::factory()->unverified()->create();
    $user->sendEmailVerificationNotification();
    $originalHash = $user->emailVerificationOtp->otp_hash;

    $this->actingAs($user)
        ->post('/email/verification-notification')
        ->assertSessionHasErrors('otp');

    $this->travel(61)->seconds();

    $this->post('/email/verification-notification')
        ->assertSessionHasNoErrors();

    expect($user->emailVerificationOtp()->value('otp_hash'))->not->toBe($originalHash);
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
