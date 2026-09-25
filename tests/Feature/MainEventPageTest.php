<?php

test('main event page renders the complete public summit narrative', function () {
    $this->get(route('main-event.index'))
        ->assertOk()
        ->assertViewIs('pages.main-event.index')
        ->assertSee('The final stage of')
        ->assertSee('29 Nov 2026')
        ->assertSee('images/icon/arrow-down-icon-mainevent.svg', false)
        ->assertSee('Contact person')
        ->assertSee('Alya Putri')
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

test('main event uses real public routes and safe placeholders for unfinished destinations', function () {
    $this->get(route('main-event.index'))
        ->assertOk()
        ->assertSee(route('competitions.index'))
        ->assertSee(route('pre-event-1.index'))
        ->assertSee('data-link-todo="summit-pass"', false)
        ->assertSee('data-link-todo="mcc-guidebook"', false)
        ->assertSee('Key Dates on the Road to 29 November.')
        ->assertSee('data-timeline-state=', false)
        ->assertSee('home-people-marquee__track', false)
        ->assertSee('data-people-filter="speaker"', false)
        ->assertSee('main-event-gradient-surface', false)
        ->assertSee('main-event-gradient-surface__scale', false)
        ->assertSee('Open guidebook')
        ->assertSee('To be announced');
});
