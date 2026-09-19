<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the filament panel uses the private catalyst path', function () {
    $this->get('/backincatalyst/login')->assertOk();
    $this->get('/admin/login')->assertNotFound();
});

test('guests are redirected to the filament login page', function () {
    $this->get('/backincatalyst')
        ->assertRedirect('/backincatalyst/login');
});

test('participants cannot access the filament panel', function () {
    $participant = User::factory()->create();

    $this->actingAs($participant)
        ->get('/backincatalyst')
        ->assertForbidden();
});

test('administrators can access the filament panel', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/backincatalyst')
        ->assertOk();
});
