<?php

use App\Models\User;
use Carbon\CarbonImmutable;
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
        ->assertSee('href="'.route('main-event.index').'#guidebook"', false)
        ->assertSee('images/brand/footer-image.webp', false)
        ->assertSeeInOrder(['Explore', 'Competition', 'Resource', 'Connect'])
        ->assertSee('Kebijakan Privasi')
        ->assertSee('Syarat dan Ketentuan');
});

test('navbar guidebook link targets the main event resource section', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('href="'.route('main-event.index').'#guidebook"', false);
});

test('homepage renders the Catalyst marketing journey in the approved public shell', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSeeInOrder([
            'Catalyst Summit',
            'Supported by',
            'About catalyst',
            'Grand theme',
            'Why Horizon',
            'The Catalyst journey',
            'Compete at Catalyst',
            'Catalyst 2026 timeline',
            'Why participate',
            'Resources',
            'People of Catalyst',
            'Partner with Catalyst',
            'FAQ',
        ])
        ->assertSee('data-navbar-theme="dark"', false)
        ->assertSee('data-navbar-theme="light"', false)
        ->assertSee('images/brand/hero-home.png', false)
        ->assertSee('images/brand/partnership-home.png', false)
        ->assertDontSee('placehold.co', false);
});

test('homepage calls to action use existing named public routes', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('href="'.route('pre-event-1.index').'"', false)
        ->assertSee('href="'.route('pre-event-2.index').'"', false)
        ->assertSee('href="'.route('main-event.index').'"', false)
        ->assertSee('href="'.route('register').'"', false);
});

test('homepage directed revisions render the shared icons anchors and motion hooks', function () {
    $response = $this->get(route('home'))
        ->assertOk()
        ->assertSee('href="#guidebook"', false)
        ->assertSee('id="guidebook"', false)
        ->assertSee('images/icon/leaf-icon-eyebrow.svg', false)
        ->assertSee('images/icon/resilient-icon-whyhorizon.svg', false)
        ->assertSee('images/icon/member-icon-competition.svg', false)
        ->assertSee('images/icon/guidebook-icon-resource.svg', false)
        ->assertSee('home-horizon__light--left', false)
        ->assertSee('home-horizon__light--right', false)
        ->assertSee('data-reveal', false)
        ->assertSee('home-people-marquee__track', false)
        ->assertSee('Florecita')
        ->assertSee('What are the three phases of Catalyst?')
        ->assertSee('Where does Catalyst Summit take place?')
        ->assertSee('logo-partner-bemfst.png', false)
        ->assertDontSee('Explore Pre-Event 1')
        ->assertDontSee('Explore Pre-Event 2');

    expect(substr_count($response->getContent(), 'Open guidebook'))->toBe(3)
        ->and(substr_count($response->getContent(), 'data-timeline-state='))->toBe(20)
        ->and(substr_count($response->getContent(), 'home-horizon__word'))->toBe(1)
        ->and(substr_count($response->getContent(), 'home-about-stat'))->toBe(3)
        ->and(substr_count($response->getContent(), 'logo-partner-'))->toBe(9);
});

test('public typography uses the licensed PP Mori font assets with real weights', function () {
    $css = file_get_contents(resource_path('css/app.css'));

    expect($css)
        ->toContain("url('../fonts/pp-mori/PPMori-Extralight.otf')")
        ->toContain("url('../fonts/pp-mori/PPMori-Regular.otf')")
        ->toContain("url('../fonts/pp-mori/PPMori-SemiBold.otf')")
        ->toContain("--font-display: 'PP Mori', ui-sans-serif")
        ->not->toContain("--font-display: 'PP Mori', 'Inter'");
});

test('homepage competition states use the Catalyst schedule in WIB', function () {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-10-05 12:00:00', 'Asia/Jakarta'));

    try {
        $response = $this->get(route('home'))->assertOk();

        expect(substr_count($response->getContent(), 'data-registration-state="registration_open"'))->toBe(1)
            ->and(substr_count($response->getContent(), 'data-registration-state="upcoming"'))->toBe(2)
            ->and(substr_count($response->getContent(), 'data-active-competition="true"'))->toBe(1);
    } finally {
        CarbonImmutable::setTestNow();
    }
});

test('public navigation destinations match the current website architecture', function () {
    $response = $this->get(route('home'))->assertOk();

    foreach (['competitions', 'guidebook', 'faq', 'timeline'] as $section) {
        $response->assertSee('href="'.route('main-event.index').'#'.$section.'"', false);
    }

    $response
        ->assertSee('href="https://instagram.com/catalyst.sreunair/"', false)
        ->assertSee('href="'.route('register').'"', false);
});

test('active competition cards animate only the Catalyst border angle', function () {
    $css = file_get_contents(resource_path('css/app.css'));

    expect($css)
        ->toContain('@property --home-active-border-angle')
        ->toContain('from var(--home-active-border-angle)')
        ->toContain('to { --home-active-border-angle: 360deg; }');
});

test('homepage closes competition registration after the configured WIB windows', function () {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-11-01 12:00:00', 'Asia/Jakarta'));

    try {
        $response = $this->get(route('home'))->assertOk();

        expect(substr_count($response->getContent(), 'data-registration-state="closed"'))->toBe(3)
            ->and(substr_count($response->getContent(), 'data-active-competition="true"'))->toBe(0);
    } finally {
        CarbonImmutable::setTestNow();
    }
});
