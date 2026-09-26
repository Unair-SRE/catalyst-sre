<?php

test('pre event one page renders the complete mentorship track narrative', function () {
    $this->get(route('pre-event-1.index'))
        ->assertOk()
        ->assertViewIs('pages.pre-event-1.index')
        ->assertSeeInOrder([
            'Catalyst Mentorship Track',
            'About Pre-Event 1',
            'Event theme',
            'Event experience',
            'Pre-Event 1 timeline',
            'People of the program',
            'Event highlights',
            'Registration',
            'Next in Catalyst',
        ], false)
        ->assertSee('Helios')
        ->assertSee('20–27 Sep')
        ->assertSee('17 Oct')
        ->assertSee('To Be Announced')
        ->assertSee('A structured mix of classes, mentoring, collaborative discussion, and final pitching')
        ->assertDontSee('placehold.co', false);
});

test('pre event one reuses the public motion and navigation contracts', function () {
    $response = $this->get(route('pre-event-1.index'))
        ->assertOk()
        ->assertSee('class="home-page', false)
        ->assertSee('data-reveal', false)
        ->assertSee('data-navbar-theme="light"', false)
        ->assertSee('data-navbar-theme="dark"', false)
        ->assertSee('images/icon/arrow-down-icon-mainevent.svg', false)
        ->assertSee('data-timeline-state=', false)
        ->assertSee('data-link-todo="mentorship-guidebook"', false)
        ->assertSee('data-content-todo="mentorship-documentation"', false)
        ->assertSee('href="'.route('register').'"', false)
        ->assertSee('href="'.route('main-event.index').'"', false)
        ->assertSee('href="'.route('pre-event-2.index').'"', false);

    expect(substr_count($response->getContent(), 'data-timeline-state='))->toBe(10);
    expect(substr_count($response->getContent(), 'data-program-person'))->toBe(8);
    expect(substr_count($response->getContent(), 'main-event-final-card'))->toBe(1);
    expect(substr_count($response->getContent(), 'images/icon/arrow-down-icon-mainevent.svg'))->toBe(1);
});
