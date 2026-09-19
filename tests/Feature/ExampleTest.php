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

test('public pages share the public navigation and footer', function (string $name, string $label) {
    $response = $this->get(route($name))
        ->assertOk()
        ->assertSee('data-public-navbar', false)
        ->assertSee('data-public-footer', false)
        ->assertSee('data-active-route="'.$name.'"', false)
        ->assertSee($label);

    if ($name === 'home') {
        $response->assertDontSee('data-nav-route="home"', false);
    } else {
        $response
            ->assertSee('data-nav-route="'.$name.'"', false)
            ->assertSee('data-active="true"', false)
            ->assertSee('aria-current="page"', false);
    }
})->with([
    'home shell' => ['home', 'Home'],
    'pre-event 1 shell' => ['pre-event-1.index', 'Pre-Event 1'],
    'pre-event 2 shell' => ['pre-event-2.index', 'Pre-Event 2'],
    'main event shell' => ['main-event.index', 'Main Event'],
]);

test('public shell exposes the approved navigation and footer information architecture', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Guidebook')
        ->assertSee('Register Now')
        ->assertSee(route('register'))
        ->assertSee('data-link-todo="guidebook"', false)
        ->assertSee('images/brand/footer-image.webp', false)
        ->assertSeeInOrder(['Explore', 'Competition', 'Resource', 'Connect'])
        ->assertSee('Kebijakan Privasi')
        ->assertSee('Syarat dan Ketentuan');
});

test('configured guidebook URL is rendered as a safe external link', function () {
    config(['services.catalyst.guidebook_url' => 'https://example.com/catalyst-guidebook']);

    $this->get(route('home'))
        ->assertOk()
        ->assertSee('href="https://example.com/catalyst-guidebook"', false)
        ->assertSee('target="_blank" rel="noopener noreferrer"', false);
});
