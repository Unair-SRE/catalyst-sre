<?php

test('main event page renders the complete public summit narrative', function () {
    $this->get(route('main-event.index'))
        ->assertOk()
        ->assertViewIs('pages.main-event.index')
        ->assertSee('The final stage of')
        ->assertSee('29 Nov 2026')
        ->assertSee('images/icon/arrow-down-icon-mainevent.svg', false)
        ->assertSee('Contact person')
        ->assertSee('Florecita')
        ->assertSeeInOrder([
            'Main Event at a glance',
            'About Catalyst Summit',
            'Summit Experience',
            'Compete at Catalyst',
            'Golden Ticket',
            'Competition Journey',
            'Talkshow',
            'Exhibition',
            'Summit Pass',
            'Main Event Timeline',
            'Guidebook and Resource',
            'Why Join Catalyst',
            'People of Catalyst Summit',
            'Sponsor and Partner',
            'FAQ',
            'Join Catalyst Summit',
        ], false)
        ->assertDontSee('22 November')
        ->assertDontSee('MCC → BCC');
});

test('main event uses registration and resource destinations with the approved partner assets', function () {
    $response = $this->get(route('main-event.index'))
        ->assertOk()
        ->assertSee(route('register'))
        ->assertSee(route('pre-event-1.index'))
        ->assertSee(route('dashboard.summit-pass.index'))
        ->assertDontSee('data-link-todo="summit-pass"', false)
        ->assertSee('data-link-todo="mcc-guidebook"', false)
        ->assertSee('href="https://instagram.com/catalyst.sreunair/"', false)
        ->assertSee('images/brand/hero-mainevent.png', false)
        ->assertSee('images/brand/logo-partner-bemfst.png', false)
        ->assertSee('What is included in the Catalyst Summit Main Event?')
        ->assertSee('What is the Catalyst Talkshow about?')
        ->assertSee('Key Dates on the Road to 29 November.')
        ->assertSee('data-timeline-state=', false)
        ->assertSee('home-people-marquee__track', false)
        ->assertSee('data-people-filter="speaker"', false)
        ->assertSee('main-event-gradient-surface', false)
        ->assertSee('main-event-gradient-surface__scale', false)
        ->assertSee('Open guidebook')
        ->assertSee('To be announced');

    expect(substr_count($response->getContent(), '<details class="group border-b border-catalyst-grey/30">'))->toBe(8);
});
