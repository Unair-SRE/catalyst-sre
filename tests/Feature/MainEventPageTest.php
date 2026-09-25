<?php

test('main event page renders the complete public summit narrative', function () {
    $this->get(route('main-event.index'))
        ->assertOk()
        ->assertViewIs('pages.main-event.index')
        ->assertSee('The final stage of')
        ->assertSee('29 November 2026')
        ->assertSeeInOrder([
            'Main Event at a glance',
            'About Catalyst Summit',
            'Summit Experience',
            'Compete at Catalyst',
            'Golden Ticket',
            'Competition Journey',
            'Talkshow',
            'Exhibition &amp; Innovation Showcase',
            'Summit Pass',
            'Catalyst 2026 Timeline',
            'Resources &amp; Rules',
            'Why Join',
            'People of Catalyst Summit',
            'Partners behind Catalyst 2026',
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
        ->assertSee('The complete road to Catalyst Summit.')
        ->assertSee('data-timeline-state=', false)
        ->assertSee('home-people-marquee__track', false)
        ->assertSee('Price')
        ->assertSee('To be announced');
});
