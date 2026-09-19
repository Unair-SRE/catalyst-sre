<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the command grants admin access to an existing user after confirmation', function () {
    $user = User::factory()->create(['email' => 'eka@example.com']);

    $this->artisan('catalyst:grant-admin', ['email' => 'EKA@example.com'])
        ->expectsConfirmation('Grant administrator access to eka@example.com?', 'yes')
        ->assertSuccessful();

    expect($user->refresh()->role)->toBe(UserRole::Admin);
});

test('the command does not change the role when confirmation is declined', function () {
    $user = User::factory()->create(['email' => 'eka@example.com']);

    $this->artisan('catalyst:grant-admin', ['email' => 'eka@example.com'])
        ->expectsConfirmation('Grant administrator access to eka@example.com?', 'no')
        ->assertSuccessful();

    expect($user->refresh()->role)->toBe(UserRole::Participant);
});

test('the command fails when the user does not exist', function () {
    $this->artisan('catalyst:grant-admin', ['email' => 'missing@example.com'])
        ->assertFailed();
});
