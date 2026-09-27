@extends('layouts.public')

@section('title', 'Catalyst Summit')

@section('content')
    @php
        $nowWib = \Carbon\CarbonImmutable::now('Asia/Jakarta');
        $instagramUrl = 'https://instagram.com/catalyst.sreunair/';

        $eventState = static function (string $startsAt, string $endsAt) use ($nowWib): string {
            $start = \Carbon\CarbonImmutable::parse($startsAt, 'Asia/Jakarta');
            $end = \Carbon\CarbonImmutable::parse($endsAt, 'Asia/Jakarta');

            return match (true) {
                $nowWib->lt($start) => 'upcoming',
                $nowWib->gt($end) => 'completed',
                default => 'ongoing',
            };
        };

        $registrationState = static function (string $startsAt, string $endsAt) use ($nowWib): string {
            $start = \Carbon\CarbonImmutable::parse($startsAt, 'Asia/Jakarta');
            $end = \Carbon\CarbonImmutable::parse($endsAt, 'Asia/Jakarta');

            return match (true) {
                $nowWib->lt($start) => 'upcoming',
                $nowWib->gt($end) => 'closed',
                default => 'registration_open',
            };
        };

        $experiences = [
            ['number' => '01', 'label' => 'Compete', 'title' => 'MCC · BCC · BPC', 'copy' => 'Choose from three tracks built around critical thinking, business strategy, and sustainable innovation.', 'cta' => 'Register for a competition', 'href' => route('register')],
            ['number' => '02', 'label' => 'Learn', 'title' => 'Catalyst Talkshow', 'copy' => 'Join a live discussion about renewable-energy deployment, implementation, and youth contribution.', 'cta' => 'Explore talkshow', 'href' => '#talkshow'],
            ['number' => '03', 'label' => 'Explore', 'title' => 'Catalyst Exhibition', 'copy' => 'Discover selected ideas, posters, and prototypes presented during the final-day exhibition.', 'cta' => 'Register for the exhibition', 'href' => route('register')],
        ];

        $competitions = [
            ['code' => 'MCC', 'name' => 'Mini Case Competition', 'audience' => 'High school students', 'members' => '3 members', 'prize' => 'IDR 2M+ prize pool', 'copy' => 'Solve a focused energy-transition case through structured analysis and a concise presentation deck.', 'registration_start' => '2026-09-27 00:00:00', 'registration_end' => '2026-10-10 23:59:59'],
            ['code' => 'BCC', 'name' => 'Business Case Competition', 'audience' => 'University students', 'members' => '3 members', 'prize' => 'IDR 9.5M prize pool', 'copy' => 'Tackle renewable-energy deployment challenges in Eastern Indonesia from analysis to final pitch.', 'registration_start' => '2026-10-11 00:00:00', 'registration_end' => '2026-10-31 23:59:59'],
            ['code' => 'BPC', 'name' => 'Business Plan Competition', 'audience' => 'University students', 'members' => '3 members', 'prize' => 'IDR 5M prize pool', 'copy' => 'Build a resilient, inclusive, carbon-conscious venture from business model to prototype.', 'registration_start' => '2026-10-11 00:00:00', 'registration_end' => '2026-10-31 23:59:59'],
        ];

        $journey = [
            ['01', 'Choose competition', 'Find the track that fits your background and team.'],
            ['02', 'Register your team', 'Complete the team information and track requirements.'],
            ['03', 'First submission', 'Submit the required case, abstract, BMC, or competition material.'],
            ['04', 'Qualification', 'Selected teams advance to the next competition stage.'],
            ['05', 'Final preparation', 'Refine the solution through technical meetings and final submissions.'],
            ['06', 'Catalyst Summit', 'Finalists pitch and selected teams showcase their work.'],
        ];

        $showcaseItems = [
            ['Business case', 'Finalist project to be announced', 'bg-catalyst-primary/10'],
            ['Business plan', 'Finalist venture to be announced', 'bg-catalyst-blue/10'],
            ['Poster showcase', 'Selected project to be announced', 'bg-catalyst-lime/25'],
            ['Prototype showcase', 'Selected project to be announced', 'bg-catalyst-green/15'],
        ];

        $timeline = [
            ['27 Sep', 'MCC Registration Opens', '2026-09-27 00:00:00', '2026-09-27 23:59:59'],
            ['05 Oct', 'MCC Case Release', '2026-10-05 00:00:00', '2026-10-05 23:59:59'],
            ['10 Oct', 'MCC Technical Meeting', '2026-10-10 00:00:00', '2026-10-10 23:59:59'],
            ['11 Oct', 'BCC & BPC Registration Opens', '2026-10-11 00:00:00', '2026-10-11 23:59:59'],
            ['17 Oct', 'MCC Submission Deadline', '2026-10-17 00:00:00', '2026-10-17 23:59:59'],
            ['24 Oct', 'MCC Winner Announcement', '2026-10-24 00:00:00', '2026-10-24 23:59:59'],
            ['31 Oct', 'BCC & BPC Registration Closes', '2026-10-31 00:00:00', '2026-10-31 23:59:59'],
            ['14 Nov', 'Semifinal Submission', '2026-11-14 00:00:00', '2026-11-14 23:59:59'],
            ['21 Nov', 'Finalist Announcement', '2026-11-21 00:00:00', '2026-11-21 23:59:59'],
            ['29 Nov', 'Final Pitch & Exhibition', '2026-11-29 00:00:00', '2026-11-29 23:59:59'],
        ];

        $guidebooks = [
            ['code' => 'MCC', 'name' => 'Mini Case Competition', 'url' => 'https://drive.google.com/drive/folders/1ZrM4_5GdXbj-MgPlj_hqkIpScZ0Z5VpK?usp=sharing', 'todo' => true],
            ['code' => 'BCC', 'name' => 'Business Case Competition', 'url' => $instagramUrl, 'todo' => false],
            ['code' => 'BPC', 'name' => 'Business Plan Competition', 'url' => $instagramUrl, 'todo' => false],
            ['code' => 'SUMMIT', 'name' => 'Summit Visitor Guide', 'url' => $instagramUrl, 'todo' => false],
        ];
        $benefits = [
            ['01', 'Real-World Challenge', 'Work on sustainability and renewable-energy problems grounded in practical contexts.', 'images/icon/realworld-whycatalyst-mainevent-icon.svg'],
            ['02', 'Expert Mentoring', 'Selected teams refine their ideas with mentors and practitioners.', 'images/icon/expert-whycatalyst-mainevent-icon.svg'],
            ['03', 'Final Pitch Exposure', 'Present solutions directly to judges, practitioners, and relevant stakeholders.', 'images/icon/exposure-whycatalyst-mainevent-icon.svg'],
            ['04', 'Exhibition Showcase', 'Selected teams can present their ideas, posters, and prototypes to a wider audience.', 'images/icon/showcase-whycatalyst-mainevent-icon.svg'],
            ['05', 'National Connections', 'Meet students, mentors, industry players, practitioners, and collaborators from different backgrounds.', 'images/icon/connection-whycatalyst-mainevent-icon.svg'],
            ['06', 'Prize & Recognition', 'Compete for IDR 16.5M+ combined prize pool, with e-certificates and additional competition benefits.', 'images/icon/prize-whycatalyst-mainevent-icon.svg'],
        ];
        $people = [['speaker', 'Speaker'], ['judges', 'Judge'], ['mentor', 'Mentor'], ['speaker', 'Speaker'], ['judges', 'Judge'], ['mentor', 'Mentor'], ['speaker', 'Speaker'], ['judges', 'Judge']];
        $partnerLogos = [
            ['name' => 'BEM FST Universitas Airlangga', 'image' => 'images/brand/logo-partner-bemfst.png'],
            ['name' => 'HIMAFI', 'image' => 'images/brand/logo-partner-himafi.jpg'],
            ['name' => 'HIMATEK HIKHAN', 'image' => 'images/brand/logo-partner-himatekhikhan.jpg'],
            ['name' => 'HIMATESDA', 'image' => 'images/brand/logo-partner-himatesda.png'],
            ['name' => 'HIMSI', 'image' => 'images/brand/logo-partner-himsi.jpg'],
            ['name' => 'HMA', 'image' => 'images/brand/logo-partner-hma.png'],
            ['name' => 'HME UA', 'image' => 'images/brand/logo-partner-hmeua.png'],
            ['name' => 'HMTL', 'image' => 'images/brand/logo-partner-hmtl.png'],
            ['name' => 'TRKB', 'image' => 'images/brand/logo-partner-trkb.png'],
        ];
        $partnerGroups = [
            ['name' => 'Strategic partners', 'logos' => [$partnerLogos[0]], 'placeholders' => 3],
            ['name' => 'Sponsors', 'logos' => [], 'placeholders' => 4],
            ['name' => 'Media partners', 'logos' => $partnerLogos, 'placeholders' => 0],
        ];
        $faqs = [
            ['What is included in the Catalyst Summit Main Event?', 'The Main Event brings together MCC, BCC, BPC, final pitching, the Catalyst Talkshow, and the Exhibition & Innovation Showcase.'],
            ['Who can join the competitions?', 'MCC is open to active SMA, SMK, or equivalent students across Indonesia. BCC and BPC are open to active D3, D4, and S1 students.'],
            ['How many members are required in a team?', 'MCC, BCC, and BPC each require three team members, including the team leader. For BCC and BPC, members may come from different majors, faculties, or universities.'],
            ['How do I register for a competition?', 'Choose your competition, complete the required team information and documents, follow the registration instructions, and submit everything before the relevant deadline. Detailed requirements are available in each competition guidebook.'],
            ['When can I submit my competition work?', 'Submission schedules differ for MCC, BCC, and BPC. Check the official guidebook for your competition for the exact stage, format, and deadline.'],
            ['What is the Summit Pass?', 'The Summit Pass is the ticket for the public Main Event experience and provides access to the Catalyst Talkshow and Exhibition. Competition registration and Summit Pass access are separate.'],
            ['What will happen at the Exhibition?', 'Selected BCC and BPC teams will showcase their ideas, posters, and prototypes to audiences, stakeholders, and potential collaborators at Universitas Airlangga.'],
            ['What is the Catalyst Talkshow about?', 'The Talkshow explores renewable energy deployment, implementation challenges, and opportunities for young people to contribute to Indonesia’s energy transition.'],
        ];
    @endphp

    <div class="home-page overflow-clip" data-home-page data-main-event-page>
        <section class="relative -mt-24 flex min-h-[40rem] items-end overflow-hidden bg-catalyst-ink pb-10 pt-36 text-white sm:-mt-28 sm:min-h-[45rem] sm:pb-14 lg:-mt-36 lg:min-h-[50rem] lg:pb-16" data-navbar-theme="light" aria-labelledby="main-event-hero-title">
            <img class="absolute inset-0 size-full object-cover object-center" src="{{ asset('images/brand/hero-mainevent.png') }}" width="1448" height="1086" fetchpriority="high" decoding="async" alt="Catalyst Summit exhibition entrance">
            <div class="absolute inset-0 bg-gradient-to-b from-catalyst-ink/15 via-catalyst-ink/35 to-catalyst-ink/90" aria-hidden="true"></div>
            <x-ui.container class="relative z-10">
                <div class="grid gap-10 border-b border-white/35 pb-10 lg:grid-cols-12 lg:items-end lg:gap-12" data-reveal>
                    <div class="lg:col-span-4"><p class="font-display text-sm font-semibold tracking-[0.02em] text-white sm:text-base">Horizon</p></div>
                    <div class="lg:col-span-8 lg:justify-self-end"><h1 id="main-event-hero-title" class="max-w-4xl font-display text-4xl font-semibold leading-[1.02] tracking-[-0.035em] sm:text-5xl lg:text-6xl">The final stage of the Catalyst journey.</h1><p class="mt-6 max-w-4xl text-base leading-7 text-white/85 sm:text-lg sm:leading-8">Competitions, final pitches, a Talkshow, and an Innovation Showcase come together in one national platform for renewable energy and sustainability.</p></div>
                </div>
                <div class="mt-5 flex items-center justify-between gap-5 text-xs font-medium tracking-[0.04em] text-white/70"><img class="size-6" src="{{ asset('images/icon/arrow-down-icon-mainevent.svg') }}" width="24" height="23" alt=""><span>Scroll to explore</span></div>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-20 lg:py-24" data-navbar-theme="dark" aria-labelledby="snapshot-title">
            <x-ui.container>
                <div class="grid gap-6 lg:grid-cols-12 lg:items-end" data-reveal><div class="lg:col-span-7"><x-public.section-label>Main Event at a glance</x-public.section-label><h2 id="snapshot-title" class="sr-only">Catalyst Summit at a glance</h2></div><p class="max-w-lg text-base leading-7 text-catalyst-muted lg:col-span-5 lg:justify-self-end">Competition, conversation, and showcase brought together in one final experience.</p></div>
                <dl class="mt-12 grid grid-cols-2 sm:mt-16 lg:grid-cols-4" data-reveal>@foreach ([['03', 'Competitions'], ['01', 'Talkshow'], ['01', 'Exhibition'], ['01', 'Summit Pass']] as [$value, $label])<div class="border-catalyst-grey/30 px-3 py-5 text-center odd:border-r sm:px-8 lg:border-r lg:px-10 lg:last:border-r-0"><dd class="font-display text-5xl font-semibold tracking-tight text-catalyst-green sm:text-6xl">{{ $value }}</dd><dt class="mt-3 text-sm font-medium text-catalyst-ink sm:text-base">{{ $label }}</dt></div>@endforeach</dl>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="about-summit-title">
            <x-ui.container><x-public.section-label>About Catalyst Summit</x-public.section-label><div class="mt-6 grid gap-10 lg:grid-cols-12 lg:gap-12" data-reveal><h2 id="about-summit-title" class="max-w-2xl font-display text-4xl font-semibold leading-[1.05] tracking-tight text-catalyst-ink sm:text-5xl lg:col-span-6">Where the Catalyst <span class="text-catalyst-grey">journey comes together.</span></h2><div class="space-y-5 text-base leading-7 text-catalyst-muted sm:text-lg sm:leading-8 lg:col-span-5 lg:col-start-8"><p>Catalyst Summit is the national-scale flagship stage of Catalyst 2026, connecting students, mentors, practitioners, industry players, and stakeholders around renewable energy.</p><p>After the pre-events, the journey continues through competition, final pitching, expert conversations, and an Innovation Showcase designed to bring promising ideas closer to real-world impact.</p></div></div></x-ui.container>
        </section>

        <section id="summit-experience" class="scroll-mt-32 bg-[#E0EDED] py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="experience-title">
            <x-ui.container><header class="max-w-3xl" data-reveal><x-public.section-label>Summit Experience</x-public.section-label><h2 id="experience-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">Three ways to experience Catalyst Summit.</h2></header><div class="mt-12 grid gap-5 md:grid-cols-3 lg:mt-16" data-reveal>@foreach ($experiences as $experience)<article class="flex min-h-[24rem] flex-col bg-white p-6 transition-transform duration-200 motion-safe:hover:-translate-y-1 sm:p-8"><div class="flex items-start justify-between gap-6"><p class="text-sm font-medium text-catalyst-primary">{{ $experience['label'] }}</p><span class="font-display text-xl font-semibold text-catalyst-lime">{{ $experience['number'] }}</span></div><h3 class="mt-12 font-display text-3xl font-semibold tracking-tight text-catalyst-ink">{{ $experience['title'] }}</h3><p class="mt-5 text-base leading-7 text-catalyst-muted">{{ $experience['copy'] }}</p><a class="group mt-auto inline-flex items-center gap-2 self-start pt-8 text-sm font-semibold text-catalyst-primary" href="{{ $experience['href'] }}">{{ $experience['cta'] }}<x-public.link-arrow class="transition-transform duration-200 group-hover:translate-x-1" /></a></article>@endforeach</div></x-ui.container>
        </section>

        <section id="competitions" class="scroll-mt-32 bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="competitions-title">
            <x-ui.container><header class="max-w-3xl" data-reveal><x-public.section-label>Compete at Catalyst</x-public.section-label><h2 id="competitions-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">Three competitions.<br>Different ways to solve.</h2><p class="mt-5 max-w-2xl text-base leading-7 text-catalyst-muted sm:text-lg">Choose the challenge that matches your background, strengths, and way of thinking.</p></header><div class="mt-12 grid gap-5 lg:mt-20 lg:grid-cols-3" data-reveal>
                @foreach ($competitions as $competition)
                    @php($state = $registrationState($competition['registration_start'], $competition['registration_end']))
                    @php($label = match ($state) { 'registration_open' => 'Registration open', 'closed' => 'Closed', default => 'Upcoming' })
                    <article
                        @class([
                            'home-competition-card group relative isolate flex min-h-[28rem] flex-col border bg-catalyst-neutral p-6 transition-transform duration-200 motion-safe:hover:-translate-y-1 sm:p-7',
                            'home-competition-card--active border-transparent' => $state === 'registration_open',
                            'border-catalyst-grey/20' => $state !== 'registration_open',
                        ])
                        data-registration-state="{{ $state }}"
                        data-active-competition="{{ $state === 'registration_open' ? 'true' : 'false' }}"
                    >
                        <span @class([
                            'self-start border px-3 py-2 text-sm font-medium',
                            'border-catalyst-green/30 bg-catalyst-green/15 text-status-success-ink' => $state === 'registration_open',
                            'border-catalyst-grey/30 bg-white text-catalyst-muted' => $state !== 'registration_open',
                        ])>{{ $label }}</span>
                        <p class="mt-8 text-xs font-semibold uppercase tracking-[0.18em] text-catalyst-primary">{{ $competition['code'] }}</p>
                        <h3 class="mt-3 font-display text-2xl font-semibold leading-tight text-catalyst-ink">{{ $competition['name'] }}</h3>
                        <p class="mt-4 text-base leading-7 text-catalyst-muted">{{ $competition['copy'] }}</p>
                        <div class="mt-auto border-t border-catalyst-grey/30 pt-5">
                            <p class="text-xs text-catalyst-muted">{{ $competition['audience'] }}</p>
                            <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-xs font-medium text-catalyst-muted">
                                <span class="inline-flex items-center gap-2"><img class="size-3.5" src="{{ asset('images/icon/member-icon-competition.svg') }}" width="14" height="14" alt="">{{ $competition['members'] }}</span>
                                <span class="inline-flex items-center gap-2"><img class="size-3.5" src="{{ asset('images/icon/prizepool-icon-whyparticipate.svg') }}" width="14" height="14" alt="">{{ $competition['prize'] }}</span>
                            </div>
                            <a class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-catalyst-primary" href="{{ route('register') }}">Register for {{ $competition['code'] }}<x-public.link-arrow /></a>
                        </div>
                    </article>
                @endforeach
            </div></x-ui.container>
        </section>

        <section class="main-event-gradient-surface relative isolate overflow-hidden py-10 sm:py-14" data-navbar-theme="dark" aria-labelledby="golden-ticket-title">
            <span class="main-event-gradient-surface__scale" aria-hidden="true"></span>
            <x-ui.container class="relative z-10"><div class="main-event-animated-border bg-white p-6 sm:p-8 lg:p-10" data-reveal><div class="grid gap-8 lg:grid-cols-12 lg:items-center"><div class="lg:col-span-8"><x-public.section-label>Golden Ticket</x-public.section-label><h2 id="golden-ticket-title" class="mt-5 font-display text-3xl font-semibold tracking-tight text-catalyst-ink sm:text-4xl">A path from Mentorship Track to Catalyst Summit.</h2><p class="mt-4 max-w-3xl text-base leading-7 text-catalyst-muted">Selected participants from Catalyst Mentorship Track may receive a Golden Ticket advancement opportunity under the confirmed program mechanism.</p></div><a class="group inline-flex min-h-12 items-center justify-center gap-2 bg-catalyst-primary px-5 py-3 text-sm font-semibold text-white hover:bg-catalyst-green lg:col-span-4 lg:justify-self-end" href="{{ route('pre-event-1.index') }}">Explore Mentorship Track<x-public.link-arrow class="brightness-0 invert transition-transform duration-200 group-hover:translate-x-1" /></a></div></div></x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="journey-title">
            <x-ui.container><header class="max-w-3xl" data-reveal><x-public.section-label>Competition Journey</x-public.section-label><h2 id="journey-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">From registration to the Summit stage.</h2></header><ol class="mt-12 grid gap-x-5 gap-y-10 sm:grid-cols-2 lg:mt-16 lg:grid-cols-3" data-reveal>@foreach ($journey as [$number, $title, $copy])<li class="min-h-40 border-t border-neutral-200 pt-5"><span class="font-display text-xl font-semibold leading-8 text-catalyst-primary">{{ $number }}</span><h3 class="mt-3 font-display text-xl font-semibold leading-8 text-catalyst-ink">{{ $title }}</h3><p class="mt-3 max-w-sm text-base leading-6 text-catalyst-muted">{{ $copy }}</p></li>@endforeach</ol></x-ui.container>
        </section>

        <section id="talkshow" class="main-event-talkshow-light scroll-mt-32 py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="talkshow-title">
            <x-ui.container class="relative z-10"><div class="grid gap-12 lg:grid-cols-12 lg:items-start lg:gap-16"><div class="lg:col-span-7" data-reveal><x-public.section-label>Talkshow</x-public.section-label><h2 id="talkshow-title" class="mt-5 max-w-2xl font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">A conversation on what it takes to move the energy transition forward.</h2><p class="mt-5 max-w-2xl text-base leading-7 text-catalyst-muted sm:text-lg">The Catalyst Talkshow brings together practitioners and changemakers to discuss implementation, collaboration, and the role young people can take in Indonesia’s energy transition.</p><a class="mt-8 inline-flex min-h-12 items-center justify-center bg-catalyst-primary px-4 py-3.5 text-sm font-semibold text-white hover:bg-catalyst-green" href="{{ route('register') }}">Get Summit Pass</a></div><article class="border border-yellow-300 bg-white p-7 sm:p-8 lg:col-span-5" data-reveal><p class="text-base leading-6 text-catalyst-primary">Get Summit Pass</p><h3 class="mt-6 font-display text-2xl font-semibold leading-8 text-catalyst-ink">CATALYST Talkshow</h3><dl class="mt-8 grid gap-x-12 gap-y-5 border-t border-yellow-300 pt-6 sm:grid-cols-2"><div><dt class="text-xs uppercase leading-5 tracking-wide text-catalyst-grey">Speaker</dt><dd class="mt-2 text-base font-semibold leading-6 text-catalyst-ink">To Be Announced</dd></div><div><dt class="text-xs uppercase leading-5 tracking-wide text-catalyst-grey">Topic</dt><dd class="mt-2 text-base font-semibold leading-6 text-catalyst-ink">To Be Announced</dd></div><div><dt class="text-xs uppercase leading-5 tracking-wide text-catalyst-grey">Date &amp; Time</dt><dd class="mt-2 text-base font-semibold leading-6 text-catalyst-ink">29 Nov 2026</dd></div></dl></article></div></x-ui.container>
        </section>

        <section id="exhibition" class="scroll-mt-32 bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="exhibition-title">
            <x-ui.container><header class="max-w-4xl" data-reveal><x-public.section-label>Exhibition</x-public.section-label><h2 id="exhibition-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">See the ideas beyond the final pitch.</h2><p class="mt-5 max-w-3xl text-base leading-7 text-catalyst-muted sm:text-lg">Selected teams present posters, prototypes, and competition ideas in an open showcase for participants, practitioners, and partners.</p></header></x-ui.container>
            <div class="home-people-marquee mt-12 lg:mt-16" aria-label="Exhibition projects to be announced" data-reveal><div class="home-people-marquee__track">@foreach ([false, true] as $duplicate)<div class="home-people-marquee__group" @if ($duplicate) aria-hidden="true" @endif>@foreach ($showcaseItems as [$category, $title, $surface])<article class="w-[17rem] shrink-0 sm:w-[20rem]"><div class="{{ $surface }} grid aspect-square place-items-center border border-catalyst-primary/15 p-7 text-center"><div><img class="mx-auto h-16 w-auto opacity-70" src="{{ asset('images/brand/catalyst-mark.png') }}" width="35" height="64" loading="lazy" decoding="async" alt=""><p class="mt-4 text-xs font-medium text-catalyst-primary">Official showcase visual<br>coming soon</p></div></div><p class="mt-4 text-xs font-semibold uppercase tracking-[0.12em] text-catalyst-primary">{{ $category }}</p><h3 class="mt-2 font-display text-lg font-semibold text-catalyst-ink">{{ $title }}</h3></article>@endforeach</div>@endforeach</div></div>
            <x-ui.container><div class="mt-10 flex flex-col items-start gap-3 sm:flex-row" data-reveal><a class="inline-flex min-h-12 items-center gap-2 bg-catalyst-primary px-5 py-3 text-sm font-semibold text-white hover:bg-catalyst-green" href="{{ route('register') }}">Join the Exhibition<x-public.link-arrow class="brightness-0 invert" /></a><a class="inline-flex min-h-12 items-center gap-2 border border-catalyst-primary px-5 py-3 text-sm font-semibold text-catalyst-primary hover:bg-catalyst-neutral" href="{{ route('register') }}">Exhibition information<x-public.link-arrow /></a></div></x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="summit-pass-title">
            <x-ui.container><div class="grid gap-12 lg:grid-cols-12 lg:items-stretch lg:gap-16"><div class="flex flex-col justify-between lg:col-span-7" data-reveal><div><x-public.section-label>Summit Pass</x-public.section-label><h2 id="summit-pass-title" class="mt-5 max-w-[32.5rem] font-display text-4xl font-semibold leading-10 tracking-tight text-catalyst-ink">One Summit Pass for<br class="hidden sm:block"> the Talkshow and Exhibition.</h2><p class="mt-8 max-w-[32.5rem] text-base leading-7 text-catalyst-muted sm:text-lg">Access Catalyst Summit’s public final-day experiences with one pass, including the Talkshow and Innovation Showcase.</p></div><p class="mt-12 border-l border-catalyst-primary bg-gradient-to-r from-yellow-300/50 to-white/0 p-4 text-base leading-6 text-catalyst-ink lg:mt-16">Competition registration is separate from the Summit Pass.</p></div><article class="bg-stone-50 p-6 sm:p-8 lg:col-span-4 lg:col-start-9" data-reveal><div class="flex items-start justify-between gap-5 border-b border-neutral-200 pb-6"><h3 class="font-display text-2xl font-semibold leading-9 text-catalyst-ink">Summit Pass</h3><span class="whitespace-nowrap bg-lime-400/30 p-2 text-sm leading-4 text-catalyst-primary">Coming Soon</span></div><ul class="mt-6 space-y-3">@foreach (['Talkshow Access', 'Exhibition Access', 'QR Check-In', 'Price to be announced'] as $feature)<li class="flex items-center gap-3 text-base leading-6 text-catalyst-ink"><img class="size-6 shrink-0" src="{{ asset('images/icon/completed-icon-timeline.svg') }}" width="24" height="24" alt="">{{ $feature }}</li>@endforeach</ul><a class="mt-6 inline-flex min-h-12 w-full items-center justify-center bg-catalyst-primary px-4 py-3.5 text-sm font-semibold text-white hover:bg-catalyst-green" href="{{ route('register') }}">Get Summit Pass</a><p class="mt-4 text-xs leading-5 text-catalyst-grey">Pricing and availability will be announced ahead of the Summit.</p></article></div></x-ui.container>
        </section>

        <section id="timeline" class="scroll-mt-32 bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="timeline-title">
            <x-ui.container><header class="max-w-3xl" data-reveal><x-public.section-label>Main Event Timeline</x-public.section-label><h2 id="timeline-title" class="mt-5 font-display text-4xl font-semibold tracking-tight text-catalyst-ink sm:text-5xl">Key Dates on the Road to 29 November.</h2></header>
                <ol class="relative mt-14 border-l border-dashed border-catalyst-primary/35 pl-9 lg:hidden" data-reveal>@foreach ($timeline as [$date, $label, $start, $end])@php($state = $eventState($start, $end))<li class="relative pb-10 last:pb-0" data-timeline-state="{{ $state }}"><span class="absolute -left-[3.28rem] -top-1 grid size-8 place-items-center bg-white" aria-hidden="true">@if ($state === 'completed')<img class="size-8" src="{{ asset('images/icon/completed-icon-timeline.svg') }}" width="32" height="32" alt="">@elseif ($state === 'ongoing')<img class="size-8" src="{{ asset('images/icon/ongoing-icon-timeline.svg') }}" width="32" height="32" alt="">@else<span class="size-5 rounded-full border border-catalyst-primary/45 bg-white"></span>@endif</span><time class="font-display text-xl font-semibold text-catalyst-ink sm:text-2xl">{{ $date }}</time><p class="mt-2 max-w-xs text-sm leading-5 text-catalyst-muted sm:text-base">{{ $label }}</p></li>@endforeach</ol>
                <div class="relative mt-20 hidden lg:block" data-reveal><span class="home-timeline__turn" aria-hidden="true"></span><ol class="home-timeline__grid grid" aria-label="Main Event milestones">@foreach ($timeline as [$date, $label, $start, $end])@php($state = $eventState($start, $end))<li class="relative z-10 pt-12" data-timeline-state="{{ $state }}"><span class="absolute left-0 top-0 grid size-8 place-items-center bg-white" aria-hidden="true">@if ($state === 'completed')<img class="size-8" src="{{ asset('images/icon/completed-icon-timeline.svg') }}" width="32" height="32" alt="">@elseif ($state === 'ongoing')<img class="size-8" src="{{ asset('images/icon/ongoing-icon-timeline.svg') }}" width="32" height="32" alt="">@else<span class="size-5 rounded-full border border-catalyst-primary/45 bg-white"></span>@endif</span><time class="font-display text-xl font-semibold text-catalyst-ink sm:text-2xl">{{ $date }}</time><p class="mt-3 max-w-[11rem] text-sm leading-5 text-catalyst-muted sm:text-base">{{ $label }}</p></li>@endforeach</ol></div>
            </x-ui.container>
        </section>

        <section id="guidebook" class="scroll-mt-32 bg-catalyst-lime/10 py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="guidebook-title">
            <x-ui.container><header class="max-w-4xl" data-reveal><x-public.section-label>Guidebook and Resource</x-public.section-label><h2 id="guidebook-title" class="mt-5 font-display text-4xl font-semibold tracking-tight text-catalyst-ink sm:text-5xl">Start with the official guidebook.</h2><p class="mt-5 max-w-3xl text-base leading-7 text-catalyst-muted sm:text-lg">Competition Rules, Eligibility, Timelines, and Submission Requirements are available in each guidebook.</p></header><div class="mt-12 grid gap-5 sm:grid-cols-2 lg:mt-16 lg:grid-cols-4" data-reveal>@foreach ($guidebooks as $guidebook)<article class="flex min-h-64 flex-col bg-white p-6 sm:p-7"><img class="size-6" src="{{ asset('images/icon/guidebook-icon-resource.svg') }}" width="24" height="24" alt=""><h3 class="mt-6 font-display text-xl font-semibold text-catalyst-ink">{{ $guidebook['code'] === 'SUMMIT' ? 'Summit Visitor Guide' : $guidebook['code'].' Guidebook' }}</h3><p class="mt-3 text-sm leading-6 text-catalyst-muted">{{ $guidebook['name'] }}</p><a class="mt-auto inline-flex items-center gap-2 self-start pt-8 text-base text-catalyst-primary" href="{{ $guidebook['url'] }}" @if ($guidebook['todo']) aria-disabled="true" data-link-todo="mcc-guidebook" title="MCC guidebook URL to be confirmed" @else target="_blank" rel="noopener noreferrer" @endif>Open guidebook<x-public.link-arrow external /></a></article>@endforeach</div></x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="benefits-title">
            <x-ui.container><header class="max-w-4xl" data-reveal><x-public.section-label>Why Join Catalyst</x-public.section-label><h2 id="benefits-title" class="mt-5 font-display text-4xl font-semibold tracking-tight text-catalyst-ink sm:text-5xl">More than a final result.</h2><p class="mt-5 max-w-3xl text-base leading-7 text-catalyst-muted sm:text-lg">The value of Catalyst goes beyond ranking. Participants gain experience, feedback, connections, and recognition throughout the journey.</p></header><ol class="mt-12 border-t border-neutral-200 lg:mt-16" data-reveal>@foreach ($benefits as [$number, $title, $copy, $icon])<li class="grid grid-cols-[3.5rem_minmax(0,1fr)] gap-x-5 gap-y-4 border-b border-neutral-200 py-7 lg:grid-cols-[3.5rem_minmax(15rem,1fr)_minmax(20rem,30rem)] lg:items-center lg:gap-x-16 xl:gap-x-28"><span class="font-display text-5xl font-semibold leading-10 text-catalyst-primary">{{ $number }}</span><div class="flex items-center gap-5"><img class="size-6 shrink-0" src="{{ asset($icon) }}" width="24" height="24" alt=""><h3 class="font-display text-xl font-semibold leading-8 text-catalyst-ink">{{ $title }}</h3></div><p class="col-start-2 text-base leading-6 text-catalyst-muted lg:col-start-auto lg:text-right">{{ $copy }}</p></li>@endforeach</ol></x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="people-title">
            <x-ui.container><header class="max-w-4xl" data-reveal><x-public.section-label>People of Catalyst Summit</x-public.section-label><h2 id="people-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">Meet the people behind the conversations and competitions.</h2><p class="mt-5 text-base leading-7 text-catalyst-muted sm:text-lg">Speakers, judges, mentors, and practitioners across Catalyst Summit.</p></header><div class="mt-8 flex flex-wrap gap-2" aria-label="Filter people by role" data-reveal>@foreach ([['all', 'All'], ['speaker', 'Speaker'], ['judges', 'Judges'], ['mentor', 'Mentor']] as [$filter, $label])<button class="min-h-11 border border-catalyst-grey/40 px-4 py-2 text-sm font-semibold text-catalyst-muted hover:border-catalyst-primary hover:text-catalyst-primary data-[active=true]:border-catalyst-primary data-[active=true]:bg-catalyst-primary data-[active=true]:text-white" type="button" data-people-filter="{{ $filter }}" data-active="{{ $filter === 'all' ? 'true' : 'false' }}" aria-pressed="{{ $filter === 'all' ? 'true' : 'false' }}">{{ $label }}</button>@endforeach</div><p class="sr-only" role="status" aria-live="polite" data-people-filter-status>{{ count($people) }} people shown</p><div class="mt-10 grid gap-x-5 gap-y-10 sm:grid-cols-2 lg:grid-cols-4" data-reveal>@foreach ($people as [$category, $role])<article data-person-card data-person-category="{{ $category }}"><div class="relative grid aspect-[3/4] place-items-center border border-catalyst-primary/20 bg-catalyst-surface p-5 text-center"><span class="absolute left-4 top-4 bg-white px-3 py-1.5 text-xs font-semibold text-catalyst-primary">{{ $role }}</span><div><img class="mx-auto h-16 w-auto opacity-70" src="{{ asset('images/brand/catalyst-mark.png') }}" width="35" height="64" loading="lazy" decoding="async" alt=""><p class="mt-4 text-xs font-medium leading-5 text-catalyst-primary">Official announcement<br>coming soon</p></div></div><h3 class="mt-4 font-display text-base font-semibold text-catalyst-ink">To Be Announced</h3></article>@endforeach</div></x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="partners-title">
            <x-ui.container><header class="max-w-3xl" data-reveal><x-public.section-label>Sponsor and Partner</x-public.section-label><h2 id="partners-title" class="mt-5 font-display text-3xl font-semibold tracking-tight text-catalyst-ink sm:text-4xl">Supported by our strategic partners.</h2></header><div class="mt-12 space-y-7" data-reveal>@foreach ($partnerGroups as $group)<div class="grid gap-4 border-t border-catalyst-grey/30 pt-5 md:grid-cols-[11rem_minmax(0,1fr)] md:items-center"><h3 class="text-xs font-semibold uppercase tracking-[0.12em] text-catalyst-muted">{{ $group['name'] }}</h3><div class="home-people-marquee main-event-partner-marquee {{ $loop->even ? 'main-event-partner-marquee--reverse' : '' }}" aria-label="{{ $group['name'] }}"><div class="home-people-marquee__track">@foreach ([false, true] as $duplicate)<div class="home-people-marquee__group" @if ($duplicate) aria-hidden="true" @endif>@foreach ($group['logos'] as $partner)<div class="flex h-20 w-48 shrink-0 items-center justify-center bg-white px-5 py-3"><img class="max-h-14 w-full max-w-36 object-contain" src="{{ asset($partner['image']) }}" width="144" height="56" loading="lazy" decoding="async" alt="{{ $partner['name'] }} logo"></div>@endforeach @foreach (range(1, $group['placeholders']) as $placeholder) @if ($group['placeholders'] > 0)<div class="flex h-20 w-48 shrink-0 items-center gap-3 px-4"><img class="h-9 w-auto opacity-45" src="{{ asset('images/brand/catalyst-mark.png') }}" width="20" height="36" loading="lazy" decoding="async" alt=""><span class="text-xs font-medium text-catalyst-grey">To be announced</span></div>@endif @endforeach</div>@endforeach</div></div></div>@endforeach</div></x-ui.container>
        </section>

        <section id="faq" class="scroll-mt-32 bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="faq-title">
            <x-ui.container><x-public.section-label>FAQ</x-public.section-label><div class="mt-6 grid gap-12 lg:grid-cols-12 lg:gap-16" data-reveal><div class="lg:col-span-4"><h2 id="faq-title" class="font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">Questions<br>before you join?</h2><div class="mt-12 text-sm leading-6 text-catalyst-muted sm:mt-28"><p class="font-semibold text-catalyst-ink">Main Event Contact person</p>
                            <p class="mt-2">Florecita</p>
                            <p>0813-3664-4639</p>
                            <p class="font-semibold text-catalyst-ink">Competition Contact person</p>
                            <p class="mt-2">Tya</p>
                            <p>0817-7239-2924</p>
                            <p class="mt-2">Shifa</p>
                            <p>0818-500-851</p></div></div><div class="border-t border-catalyst-grey/30 lg:col-span-8">@foreach ($faqs as [$question, $answer])<details class="group border-b border-catalyst-grey/30"><summary class="flex min-h-20 list-none items-center justify-between gap-6 py-5 text-left font-display text-lg font-semibold text-catalyst-ink [&::-webkit-details-marker]:hidden"><span>{{ $question }}</span><span class="text-2xl font-normal text-catalyst-primary transition-transform duration-200 group-open:rotate-45" aria-hidden="true">+</span></summary><p class="max-w-2xl pb-6 pr-10 text-sm leading-7 text-catalyst-muted">{{ $answer }}</p></details>@endforeach</div></div></x-ui.container>
        </section>

        <section class="bg-white py-10 sm:py-16" data-navbar-theme="dark" aria-labelledby="final-cta-title">
            <x-ui.container><div class="main-event-final-card overflow-hidden border border-catalyst-primary/15 p-7 sm:p-10 lg:p-14" data-reveal><div class="max-w-4xl"><x-public.section-label>Join Catalyst Summit</x-public.section-label><h2 id="final-cta-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">Choose how you’ll be part of the Summit.</h2><p class="mt-5 max-w-2xl text-base leading-7 text-catalyst-muted">Compete through MCC, BCC, or BPC, or join the final-day Talkshow and Exhibition with a Summit Pass.</p><div class="mt-8 flex flex-col items-start gap-3 sm:flex-row"><a class="group inline-flex min-h-12 items-center justify-center gap-2 bg-catalyst-primary px-5 py-3 text-sm font-semibold text-white hover:bg-catalyst-green" href="{{ route('register') }}">Register for a competition<x-public.link-arrow class="brightness-0 invert transition-transform duration-200 group-hover:translate-x-1" /></a><a class="inline-flex min-h-12 items-center justify-center border border-catalyst-primary px-5 py-3 text-sm font-semibold text-catalyst-primary hover:bg-white/70" href="{{ route('register') }}">Get Summit Pass</a></div></div></div></x-ui.container>
        </section>
    </div>
@endsection
