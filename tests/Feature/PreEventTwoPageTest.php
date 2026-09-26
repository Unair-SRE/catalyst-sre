<?php

test('pre event two page renders the complete green action narrative', function () {
    $this->get(route('pre-event-2.index'))
        ->assertOk()
        ->assertViewIs('pages.pre-event-2.index')
        ->assertSeeInOrder([
            'Catalyst Green Action',
            'About Pre-Event 2',
            'Event theme',
            'Event experience',
            'Pre-Event 2 details',
            'What Participants Got',
            'Event highlights',
            'In collaboration with',
            'Next in Catalyst',
        ], false)
        ->assertSee('Synergy')
        ->assertSee('Surabaya')
        ->assertSee('Ekowisata Mangrove Wonorejo')
        ->assertSee('IDR 38K')
        ->assertSee('13 Sep')
        ->assertSee('Green Action × HEROGREEN 2026')
        ->assertDontSee('Pre-Event 1 timeline')
        ->assertDontSee('People of the program')
        ->assertDontSee('placehold.co', false);
});

test('pre event two reuses the public page and main event section contracts', function () {
    $response = $this->get(route('pre-event-2.index'))
        ->assertOk()
        ->assertSee('class="home-page', false)
        ->assertSee('data-reveal', false)
        ->assertSee('data-navbar-theme="light"', false)
        ->assertSee('data-navbar-theme="dark"', false)
        ->assertSee('images/icon/arrow-down-icon-mainevent.svg', false)
        ->assertSee('data-content-todo="green-action-programme-photography"', false)
        ->assertSee('data-content-todo="green-action-documentation"', false)
        ->assertSee('href="'.route('main-event.index').'"', false)
        ->assertSee('href="'.route('pre-event-1.index').'"', false)
        ->assertDontSee('data-timeline-state=', false)
        ->assertDontSee('data-program-person', false);

    expect(substr_count($response->getContent(), 'data-green-action-fact'))->toBe(3);
    expect(substr_count($response->getContent(), 'data-participant-benefit'))->toBe(4);
    expect(substr_count($response->getContent(), 'main-event-final-card'))->toBe(1);
    expect(substr_count($response->getContent(), 'images/icon/arrow-down-icon-mainevent.svg'))->toBe(1);
});
