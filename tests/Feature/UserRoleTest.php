<?php

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('new users are participants by default', function () {
    $user = User::factory()->create();

    expect($user->role)->toBe(UserRole::Participant)
        ->and($user->isParticipant())->toBeTrue()
        ->and($user->isAdmin())->toBeFalse();
});

test('the user factory can create an admin', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->role)->toBe(UserRole::Admin)
        ->and($admin->isAdmin())->toBeTrue()
        ->and($admin->isParticipant())->toBeFalse();
});

test('role cannot be changed through mass assignment', function () {
    $user = User::factory()->create();

    $user->fill(['role' => UserRole::Admin->value])->save();

    expect($user->refresh()->role)->toBe(UserRole::Participant);
});
