<?php

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('the filament panel uses the private catalyst path', function () {
    $this->get('/backincatalyst/login')
        ->assertOk()
        ->assertSee('Catalyst Admin')
        ->assertDontSee('Filament v');
    $this->get('/admin/login')->assertNotFound();
});

test('administrators can browse users and open a dedicated detail page', function () {
    $admin = User::factory()->admin()->create();
    $participant = User::factory()->create();

    $this->actingAs($admin)
        ->get(UserResource::getUrl('index'))
        ->assertOk()
        ->assertSee($participant->name);

    $this->actingAs($admin)
        ->get(UserResource::getUrl('view', ['record' => $participant]))
        ->assertOk()
        ->assertSee($participant->email);
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

test('administrators can configure summit ticket sales in filament', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/backincatalyst/payment-settings')
        ->assertOk()
        ->assertSee('Summit Sales Settings')
        ->assertSee('Configure Summit sales');
});
