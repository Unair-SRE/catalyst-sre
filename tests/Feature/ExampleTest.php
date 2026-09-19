<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('frontend foundation routes render their assigned views', function (string $name, string $path, string $view) {
    expect(route($name, [], false))->toBe($path);

    if (str_starts_with($name, 'dashboard.')) {
        $this->actingAs(User::factory()->create());
    }

    $this->get(route($name))
        ->assertOk()
        ->assertViewIs($view);
})->with([
    'home' => ['home', '/', 'pages.home.index'],
    'pre-event 1' => ['pre-event-1.index', '/pre-event-1', 'pages.pre-event-1.index'],
    'pre-event 2' => ['pre-event-2.index', '/pre-event-2', 'pages.pre-event-2.index'],
    'main event' => ['main-event.index', '/main-event', 'pages.main-event.index'],
    'dashboard prototype' => ['dashboard.index', '/dashboard', 'dashboard.index'],
]);
