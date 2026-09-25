@extends('layouts.public')

@section('title', 'Catalyst Summit')

@section('content')
    @php
        $now = \Carbon\CarbonImmutable::now('Asia/Jakarta');

        $timeline = [
            ['date' => '13 Sep', 'title' => 'Green Action Challenge', 'starts_at' => '2026-09-13 00:00:00', 'ends_at' => '2026-09-13 23:59:59'],
            ['date' => '20 Sep', 'title' => 'Mentorship Registration Opens', 'starts_at' => '2026-09-20 00:00:00', 'ends_at' => '2026-10-04 23:59:59'],
            ['date' => '27 Sep', 'title' => 'MCC Registration Opens', 'starts_at' => '2026-09-27 00:00:00', 'ends_at' => '2026-10-10 23:59:59'],
            ['date' => '11 Oct', 'title' => 'BCC & BPC Registration Opens', 'starts_at' => '2026-10-11 00:00:00', 'ends_at' => '2026-10-31 23:59:59'],
            ['date' => '17 Oct', 'title' => 'MCC Submission Deadline', 'starts_at' => '2026-10-17 00:00:00', 'ends_at' => '2026-10-17 23:59:59'],
            ['date' => '24 Oct', 'title' => 'MCC Winner Announcement', 'starts_at' => '2026-10-24 00:00:00', 'ends_at' => '2026-10-24 23:59:59'],
            ['date' => '31 Oct', 'title' => 'BCC & BPC Registration Closes', 'starts_at' => '2026-10-31 00:00:00', 'ends_at' => '2026-10-31 23:59:59'],
            ['date' => '14 Nov', 'title' => 'Semifinal Submission', 'starts_at' => '2026-11-14 00:00:00', 'ends_at' => '2026-11-14 23:59:59'],
            ['date' => '21 Nov', 'title' => 'Finalist Announcement', 'starts_at' => '2026-11-21 00:00:00', 'ends_at' => '2026-11-21 23:59:59'],
            ['date' => '29 Nov', 'title' => 'Final Pitch & Exhibition', 'starts_at' => '2026-11-29 00:00:00', 'ends_at' => '2026-11-29 23:59:59'],
        ];

        $eventState = static function (array $event) use ($now): string {
            $startsAt = \Carbon\CarbonImmutable::parse($event['starts_at'], 'Asia/Jakarta');
            $endsAt = \Carbon\CarbonImmutable::parse($event['ends_at'], 'Asia/Jakarta');

            return match (true) {
                $now->lt($startsAt) => 'upcoming',
                $now->gt($endsAt) => 'completed',
                default => 'ongoing',
            };
        };

        $statusLabel = static fn (string $state): string => match ($state) {
            'completed' => 'Completed',
            'ongoing' => 'Ongoing',
            default => 'Upcoming',
        };

        $competitions = [
            ['code' => 'MCC', 'tag' => 'High school track', 'title' => 'Mini Case Competition', 'description' => 'Analyze a focused energy-transition case and turn your solution into a concise, high-impact presentation.', 'members' => '3 members', 'prize' => 'IDR 2M+ prize pool', 'registration' => '27 Sep–10 Oct', 'starts_at' => '2026-09-27 00:00:00', 'ends_at' => '2026-10-10 23:59:59'],
            ['code' => 'BCC', 'tag' => 'University track', 'title' => 'Business Case Competition', 'description' => 'Tackle renewable-energy deployment challenges in Eastern Indonesia, from initial analysis to final pitch.', 'members' => '3 members', 'prize' => 'IDR 9.5M prize pool', 'registration' => '11–31 Oct', 'starts_at' => '2026-10-11 00:00:00', 'ends_at' => '2026-10-31 23:59:59'],
            ['code' => 'BPC', 'tag' => 'University track', 'title' => 'Business Plan Competition', 'description' => 'Build a resilient, inclusive, and carbon-conscious venture from Business Model Canvas to final pitch.', 'members' => '3 members', 'prize' => 'IDR 5M prize pool', 'registration' => '11–31 Oct', 'starts_at' => '2026-10-11 00:00:00', 'ends_at' => '2026-10-31 23:59:59'],
        ];

        $experiences = [
            ['number' => '01', 'label' => 'Compete', 'title' => 'MCC · BCC · BPC', 'description' => 'Choose from three tracks built around critical thinking, business strategy, and sustainable innovation.', 'cta' => 'Explore competitions', 'href' => route('competitions.index')],
            ['number' => '02', 'label' => 'Learn', 'title' => 'Catalyst Talkshow', 'description' => 'Join a live discussion about renewable-energy deployment, implementation, and youth contribution.', 'cta' => 'Explore talkshow', 'href' => '#talkshow'],
            ['number' => '03', 'label' => 'Explore', 'title' => 'Innovation Showcase', 'description' => 'Discover selected ideas, posters, and prototypes presented during the final-day exhibition.', 'cta' => 'Explore exhibition', 'href' => '#exhibition'],
        ];

        $journey = [
            ['number' => '01', 'title' => 'Choose competition', 'description' => 'Find the track that fits your background and team.'],
            ['number' => '02', 'title' => 'Register your team', 'description' => 'Complete your team information and requirements.'],
            ['number' => '03', 'title' => 'First submission', 'description' => 'Submit the required case, abstract, BMC, or competition material.'],
            ['number' => '04', 'title' => 'Qualification', 'description' => 'Selected teams advance to the next competition stage.'],
            ['number' => '05', 'title' => 'Final preparation', 'description' => 'Refine the solution through mentoring, technical meetings, or final submissions.'],
            ['number' => '06', 'title' => 'Catalyst Summit', 'description' => 'Finalists pitch and selected teams showcase their work.'],
        ];

        $showcaseItems = [
            ['label' => 'BCC finalist', 'title' => 'Selected team / project', 'surface' => 'bg-catalyst-primary/10'],
            ['label' => 'BPC finalist', 'title' => 'Selected team / project', 'surface' => 'bg-catalyst-blue/10'],
            ['label' => 'Poster showcase', 'title' => 'Selected project', 'surface' => 'bg-catalyst-lime/25'],
            ['label' => 'Prototype showcase', 'title' => 'Selected project', 'surface' => 'bg-catalyst-green/15'],
        ];

        $guidebooks = [
            ['code' => 'MCC', 'title' => 'MCC Guidebook', 'description' => 'Mini Case Competition rules and submission details.'],
            ['code' => 'BCC', 'title' => 'BCC Guidebook', 'description' => 'Competition stages, scoring, and final pitch requirements.'],
            ['code' => 'BPC', 'title' => 'BPC Guidebook', 'description' => 'The journey from BMC submission to final showcase.'],
            ['code' => 'SUMMIT', 'title' => 'Summit Visitor Guide', 'description' => 'Talkshow, Exhibition, access, and event-day information.'],
        ];

        $benefits = [
            ['number' => '01', 'title' => 'Real-world challenges', 'description' => 'Work on sustainability and renewable-energy problems grounded in practical contexts.'],
            ['number' => '02', 'title' => 'Expert mentoring', 'description' => 'Selected teams refine their ideas with mentors and practitioners.'],
            ['number' => '03', 'title' => 'Final pitch exposure', 'description' => 'Present solutions directly to judges, practitioners, and relevant stakeholders.'],
            ['number' => '04', 'title' => 'Exhibition showcase', 'description' => 'Selected teams can present their ideas, posters, and prototypes to a wider audience.'],
            ['number' => '05', 'title' => 'National connections', 'description' => 'Meet students, mentors, industry players, practitioners, and collaborators.'],
            ['number' => '06', 'title' => 'Prize & recognition', 'description' => 'Compete for the combined prize pool, e-certificates, and competition benefits.'],
        ];

        $people = [
            ['role' => 'Talkshow speaker', 'name' => 'To be announced', 'organization' => 'Speaker announcement'],
            ['role' => 'Competition judge', 'name' => 'To be announced', 'organization' => 'Judge announcement'],
            ['role' => 'Competition mentor', 'name' => 'To be announced', 'organization' => 'Mentor announcement'],
            ['role' => 'Energy practitioner', 'name' => 'To be announced', 'organization' => 'Practitioner announcement'],
            ['role' => 'Talkshow speaker', 'name' => 'To be announced', 'organization' => 'Speaker announcement'],
            ['role' => 'Competition judge', 'name' => 'To be announced', 'organization' => 'Judge announcement'],
            ['role' => 'Competition mentor', 'name' => 'To be announced', 'organization' => 'Mentor announcement'],
            ['role' => 'Industry representative', 'name' => 'To be announced', 'organization' => 'Partner announcement'],
        ];

        $faqs = [
            ['question' => 'What is Catalyst Summit?', 'answer' => 'Catalyst Summit is the main event of Catalyst 2026, bringing together MCC, BCC, BPC, final pitching, a Talkshow, and an Exhibition & Innovation Showcase.'],
            ['question' => 'Who can join the competitions?', 'answer' => 'MCC is open to active high school students. BCC and BPC are open to active university students. Refer to each official guidebook for the complete eligibility rules.'],
            ['question' => 'How many members are required in a team?', 'answer' => 'MCC, BCC, and BPC each require three members including the team leader. Refer to the official guidebook for track-specific requirements.'],
            ['question' => 'Does competition registration include a Summit Pass?', 'answer' => 'No. Competition registration and Summit Pass access are separate.'],
            ['question' => 'What does the Summit Pass include?', 'answer' => 'One Summit Pass gives access to the Catalyst Talkshow and Exhibition.'],
            ['question' => 'Where can I find the complete competition rules?', 'answer' => 'Open the official MCC, BCC, or BPC guidebook for stage-specific rules, submissions, scoring, and deadlines.'],
        ];
    @endphp

    <div class="home-page overflow-clip" data-home-page>
        <section class="relative -mt-24 flex min-h-[48rem] items-end overflow-hidden bg-catalyst-ink pb-14 pt-40 text-white sm:-mt-28 sm:min-h-[54rem] sm:pb-20 lg:-mt-36 lg:min-h-[64rem] lg:pb-24" data-navbar-theme="light" aria-labelledby="main-event-hero-title">
            <img class="absolute inset-0 size-full object-cover object-center" src="{{ asset('images/brand/footer-image.webp') }}" width="1440" height="700" fetchpriority="high" decoding="async" alt="Solar panels beneath an open sky">
            <div class="absolute inset-0 bg-gradient-to-b from-catalyst-ink/20 via-catalyst-ink/45 to-catalyst-ink/95" aria-hidden="true"></div>
            <x-ui.container class="relative z-10">
                <div class="grid gap-9 border-b border-white/35 pb-10 lg:grid-cols-12 lg:gap-10">
                    <div class="lg:col-span-3" data-reveal>
                        <p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-white"><span class="size-1.5 rotate-45 bg-catalyst-lime" aria-hidden="true"></span>Main Event</p>
                        <p class="mt-5 font-display text-2xl font-semibold tracking-tight text-catalyst-lime">Horizon</p>
                        <p class="mt-3 max-w-56 text-sm leading-6 text-white/70">29 November 2026<br>Universitas Airlangga, Surabaya</p>
                    </div>
                    <div class="lg:col-span-9" data-reveal>
                        <h1 id="main-event-hero-title" class="max-w-5xl font-display text-5xl font-semibold leading-[0.96] tracking-[-0.04em] sm:text-6xl md:text-7xl lg:text-[5.75rem]">The final stage of the Catalyst journey.</h1>
                        <div class="mt-8 grid gap-7 md:grid-cols-9 md:items-end">
                            <p class="max-w-2xl text-base leading-7 text-white/85 sm:text-lg sm:leading-8 md:col-span-6">Competitions, final pitches, a Talkshow, and an Innovation Showcase come together in one national platform for renewable energy and sustainability.</p>
                            <a class="group inline-flex min-h-12 items-center gap-3 justify-self-start bg-catalyst-primary px-5 py-3 text-sm font-semibold text-white transition-colors duration-200 hover:bg-catalyst-green focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-white md:col-span-3 md:justify-self-end" href="#summit-experience">Explore the Summit<svg class="size-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M3 8h9M8.5 4.5 12 8l-3.5 3.5" /></svg></a>
                        </div>
                    </div>
                </div>
                <p class="mt-5 flex items-center gap-3 text-xs font-medium uppercase tracking-[0.16em] text-white/65"><span class="h-px w-10 bg-white/40" aria-hidden="true"></span>Scroll to explore</p>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="snapshot-title">
            <x-ui.container>
                <div class="grid gap-7 lg:grid-cols-12 lg:items-end" data-reveal>
                    <div class="lg:col-span-7"><p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-ink"><span class="size-1.5 rotate-45 bg-catalyst-primary" aria-hidden="true"></span>Main Event at a glance</p><h2 id="snapshot-title" class="sr-only">Catalyst Summit at a glance</h2></div>
                    <p class="max-w-xl text-base leading-7 text-catalyst-muted lg:col-span-5 lg:justify-self-end">Competition, conversation, and showcase brought together in one final experience.</p>
                </div>
                <dl class="mt-12 grid grid-cols-2 sm:mt-16 lg:grid-cols-4" data-reveal>
                    @foreach ([['03', 'Competitions'], ['01', 'Talkshow'], ['01', 'Exhibition'], ['01', 'Catalyst Summit']] as [$value, $label])
                        <div class="py-5 odd:border-r odd:border-catalyst-ink/15 even:pl-6 sm:px-8 lg:border-r lg:border-catalyst-ink/15 lg:px-10 lg:first:pl-0 lg:last:border-r-0 lg:last:pr-0"><dd class="font-display text-5xl font-semibold tracking-tight text-catalyst-primary sm:text-6xl">{{ $value }}</dd><dt class="mt-3 text-sm font-medium text-catalyst-ink sm:text-base">{{ $label }}</dt></div>
                    @endforeach
                </dl>
            </x-ui.container>
        </section>

        <section id="about-summit" class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="about-summit-title">
            <x-ui.container>
                <p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-ink" data-reveal><span class="size-1.5 rotate-45 bg-catalyst-primary" aria-hidden="true"></span>About Catalyst Summit</p>
                <div class="mt-7 grid gap-10 lg:grid-cols-12 lg:gap-12" data-reveal>
                    <h2 id="about-summit-title" class="max-w-2xl font-display text-4xl font-semibold leading-[1.05] tracking-tight text-catalyst-ink sm:text-5xl lg:col-span-6">Where the Catalyst journey <span class="text-catalyst-grey">comes together.</span></h2>
                    <div class="space-y-6 text-base leading-7 text-catalyst-ink/80 sm:text-lg sm:leading-8 lg:col-span-5 lg:col-start-8"><p>Catalyst Summit is the national-scale flagship stage of Catalyst 2026, connecting students, mentors, practitioners, industry players, and stakeholders around renewable energy.</p><p>After the pre-events, the journey continues through competition, final pitching, expert conversations, and an Innovation Showcase designed to bring promising ideas closer to real-world impact.</p></div>
                </div>
            </x-ui.container>
        </section>

        <section id="summit-experience" class="bg-catalyst-neutral py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="experience-title">
            <x-ui.container>
                <header class="max-w-3xl" data-reveal><p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-ink"><span class="size-1.5 rotate-45 bg-catalyst-primary" aria-hidden="true"></span>Summit Experience</p><h2 id="experience-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">Three ways to experience Catalyst Summit.</h2></header>
                <div class="mt-12 grid border-l border-t border-catalyst-ink/15 md:grid-cols-3" data-reveal>
                    @foreach ($experiences as $experience)
                        <article class="flex min-h-[25rem] flex-col border-b border-r border-catalyst-ink/15 bg-white p-6 transition-transform duration-200 motion-safe:hover:-translate-y-1 sm:p-8">
                            <div class="flex items-start justify-between gap-6"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-primary">{{ $experience['label'] }}</p><span class="font-display text-xl font-semibold text-catalyst-lime">{{ $experience['number'] }}</span></div>
                            <h3 class="mt-12 font-display text-3xl font-semibold tracking-tight text-catalyst-ink">{{ $experience['title'] }}</h3><p class="mt-5 text-base leading-7 text-catalyst-muted">{{ $experience['description'] }}</p>
                            <a class="group mt-auto inline-flex items-center gap-2 border-t border-catalyst-ink/15 pt-6 text-sm font-semibold text-catalyst-primary" href="{{ $experience['href'] }}">{{ $experience['cta'] }}<svg class="size-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M3 8h9M8.5 4.5 12 8l-3.5 3.5" /></svg></a>
                        </article>
                    @endforeach
                </div>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="competitions-title">
            <x-ui.container>
                <header class="grid gap-7 lg:grid-cols-12 lg:items-end" data-reveal><div class="lg:col-span-8"><p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-ink"><span class="size-1.5 rotate-45 bg-catalyst-primary" aria-hidden="true"></span>Compete at Catalyst</p><h2 id="competitions-title" class="mt-5 max-w-3xl font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">Three competitions.<br>Different ways to solve.</h2></div><p class="max-w-lg text-base leading-7 text-catalyst-muted lg:col-span-4">Choose the challenge that matches your background, strengths, and way of thinking.</p></header>
                <div class="mt-12 grid gap-5 lg:grid-cols-3" data-reveal>
                    @foreach ($competitions as $competition)
                        @php $registrationState = $eventState($competition); @endphp
                        <article @class(['home-competition-card group flex min-h-[31rem] flex-col border bg-catalyst-neutral p-6 transition-transform duration-200 motion-safe:hover:-translate-y-1 sm:p-8', 'home-competition-card--active border-transparent' => $registrationState === 'ongoing', 'border-catalyst-ink/15' => $registrationState !== 'ongoing']) data-registration-state="{{ $registrationState }}">
                            <div class="flex items-start justify-between gap-5"><p class="text-xs font-semibold uppercase tracking-[0.14em] text-catalyst-primary">{{ $competition['tag'] }}</p><span @class(['text-xs font-semibold uppercase tracking-[0.12em]', 'text-catalyst-primary' => $registrationState === 'ongoing', 'text-catalyst-muted' => $registrationState !== 'ongoing'])>{{ $statusLabel($registrationState) }}</span></div>
                            <p class="mt-10 font-display text-xl font-semibold text-catalyst-primary">{{ $competition['code'] }}</p><h3 class="mt-3 font-display text-3xl font-semibold leading-tight tracking-tight text-catalyst-ink">{{ $competition['title'] }}</h3><p class="mt-5 text-base leading-7 text-catalyst-muted">{{ $competition['description'] }}</p>
                            <dl class="mt-8 grid grid-cols-2 gap-5 border-t border-catalyst-ink/15 pt-6 text-sm"><div><dt class="text-catalyst-muted">Team</dt><dd class="mt-1 font-semibold text-catalyst-ink">{{ $competition['members'] }}</dd></div><div><dt class="text-catalyst-muted">Prize</dt><dd class="mt-1 font-semibold text-catalyst-ink">{{ $competition['prize'] }}</dd></div><div class="col-span-2"><dt class="text-catalyst-muted">Registration</dt><dd class="mt-1 font-semibold text-catalyst-ink">{{ $competition['registration'] }}</dd></div></dl>
                            <a class="mt-auto inline-flex items-center gap-2 pt-9 text-sm font-semibold text-catalyst-primary" href="{{ route('competitions.index') }}">Explore {{ $competition['code'] }}<svg class="size-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M3 8h9M8.5 4.5 12 8l-3.5 3.5" /></svg></a>
                        </article>
                    @endforeach
                </div>
            </x-ui.container>
        </section>

        <section class="bg-white pb-16 sm:pb-24 lg:pb-28" data-navbar-theme="dark" aria-labelledby="golden-ticket-title">
            <x-ui.container>
                <div class="home-partnership relative overflow-hidden border border-catalyst-primary/25 bg-catalyst-surface px-6 py-10 sm:px-10 sm:py-12 lg:grid lg:grid-cols-12 lg:items-end lg:gap-10 lg:px-14" data-reveal>
                    <div class="relative z-10 lg:col-span-8"><p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-ink"><span class="size-1.5 rotate-45 bg-catalyst-lime" aria-hidden="true"></span>Golden Ticket</p><h2 id="golden-ticket-title" class="mt-5 max-w-3xl font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">A path from Mentorship Track to Catalyst Summit.</h2><p class="mt-6 max-w-2xl text-base leading-7 text-catalyst-ink/75">Top-performing teams from Catalyst Mentorship Track can earn a Golden Ticket and advance into the wider Catalyst Summit journey.</p></div>
                    <a class="group relative z-10 mt-8 inline-flex items-center gap-2 text-sm font-semibold text-catalyst-primary lg:col-span-4 lg:mt-0 lg:justify-self-end" href="{{ route('pre-event-1.index') }}">Explore Mentorship Track<svg class="size-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M3 8h9M8.5 4.5 12 8l-3.5 3.5" /></svg></a>
                </div>
            </x-ui.container>
        </section>

        <section class="bg-catalyst-surface py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="journey-title">
            <x-ui.container>
                <header class="grid gap-7 lg:grid-cols-12 lg:items-end" data-reveal><div class="lg:col-span-8"><p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-ink"><span class="size-1.5 rotate-45 bg-catalyst-primary" aria-hidden="true"></span>Competition Journey</p><h2 id="journey-title" class="mt-5 max-w-3xl font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">From registration to the Summit stage.</h2></div><p class="text-base leading-7 text-catalyst-muted lg:col-span-4">Exact stages vary by competition. Follow the official guidebook for track-specific requirements.</p></header>
                <ol class="mt-12 grid md:grid-cols-2 lg:grid-cols-3" data-reveal>
                    @foreach ($journey as $step)
                        <li class="border-b border-catalyst-primary/25 py-8 md:px-7 md:odd:pl-0 md:even:border-l lg:odd:pl-7 lg:even:border-l-0 lg:nth-[3n+1]:border-l-0 lg:nth-[3n+1]:pl-0 lg:nth-[3n+2]:border-l lg:nth-[3n+3]:border-l"><span class="font-display text-3xl font-semibold text-catalyst-primary">{{ $step['number'] }}</span><h3 class="mt-8 font-display text-2xl font-semibold tracking-tight text-catalyst-ink">{{ $step['title'] }}</h3><p class="mt-4 max-w-sm text-sm leading-6 text-catalyst-muted">{{ $step['description'] }}</p></li>
                    @endforeach
                </ol>
            </x-ui.container>
        </section>

        <section id="talkshow" class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="talkshow-title">
            <x-ui.container>
                <div class="grid gap-12 lg:grid-cols-12 lg:gap-16">
                    <div class="lg:col-span-7" data-reveal><p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-ink"><span class="size-1.5 rotate-45 bg-catalyst-primary" aria-hidden="true"></span>Talkshow</p><h2 id="talkshow-title" class="mt-5 max-w-3xl font-display text-4xl font-semibold leading-[1.08] tracking-tight text-catalyst-ink sm:text-5xl lg:text-6xl">A conversation on what it takes to move the energy transition forward.</h2><p class="mt-7 max-w-2xl text-base leading-7 text-catalyst-muted sm:text-lg sm:leading-8">The Catalyst Talkshow brings speakers from relevant sectors into a discussion on renewable-energy deployment, implementation challenges, and the role of young people in accelerating Indonesia’s energy transition.</p><span class="mt-8 inline-flex min-h-12 cursor-not-allowed items-center bg-catalyst-primary px-5 py-3 text-sm font-semibold text-white opacity-60" role="link" aria-disabled="true" data-link-todo="summit-pass">Get Summit Pass</span></div>
                    <aside class="bg-gradient-to-br from-catalyst-lime/35 via-catalyst-surface to-catalyst-green/15 p-6 sm:p-8 lg:col-span-5" data-reveal aria-label="Catalyst Talkshow information"><p class="text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-primary">Catalyst Talkshow</p><p class="mt-16 font-display text-4xl font-semibold tracking-tight text-catalyst-ink">29 November 2026</p><p class="mt-2 text-base text-catalyst-muted">Universitas Airlangga</p><dl class="mt-10 divide-y divide-catalyst-ink/15 border-y border-catalyst-ink/15">@foreach ([['Topic', 'To be announced'], ['Speakers', 'To be announced'], ['Time', 'To be announced']] as [$label, $value])<div class="grid grid-cols-[6rem_1fr] gap-4 py-4 text-sm"><dt class="text-catalyst-muted">{{ $label }}</dt><dd class="font-semibold text-catalyst-ink">{{ $value }}</dd></div>@endforeach</dl></aside>
                </div>
            </x-ui.container>
        </section>

        <section id="exhibition" class="bg-catalyst-neutral py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="exhibition-title">
            <x-ui.container>
                <header class="grid gap-8 lg:grid-cols-12 lg:items-end" data-reveal><div class="lg:col-span-7"><p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-ink"><span class="size-1.5 rotate-45 bg-catalyst-primary" aria-hidden="true"></span>Exhibition & Innovation Showcase</p><h2 id="exhibition-title" class="mt-5 max-w-3xl font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">See the ideas beyond the final pitch.</h2></div><p class="max-w-xl text-base leading-7 text-catalyst-muted lg:col-span-5">Selected BCC and BPC teams will showcase their ideas, posters, and prototypes to audiences, stakeholders, and potential collaborators at Universitas Airlangga.</p></header>
                <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-12" data-reveal>
                    @foreach ($showcaseItems as $index => $item)
                        <article @class(['group overflow-hidden bg-white transition-transform duration-200 motion-safe:hover:-translate-y-1', 'lg:col-span-4' => in_array($index, [0, 3], true), 'lg:col-span-2' => in_array($index, [1, 2], true)])><div class="relative aspect-[4/5] overflow-hidden {{ $item['surface'] }}" aria-hidden="true"><span class="absolute right-5 top-5 font-display text-5xl font-semibold text-catalyst-primary/20">0{{ $index + 1 }}</span><span class="absolute inset-x-5 bottom-5 h-px origin-left bg-catalyst-primary/30 transition-transform duration-300 group-hover:scale-x-75"></span></div><div class="p-5"><p class="text-xs font-semibold uppercase tracking-[0.14em] text-catalyst-primary">{{ $item['label'] }}</p><h3 class="mt-3 font-display text-xl font-semibold text-catalyst-ink">{{ $item['title'] }}</h3></div></article>
                    @endforeach
                </div>
                <div class="mt-9 flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between" data-reveal><p class="max-w-xl text-sm leading-6 text-catalyst-muted">Final exhibitor lineup will be announced closer to the event.</p><div class="flex flex-col gap-3 sm:flex-row"><span class="inline-flex min-h-12 cursor-not-allowed items-center justify-center bg-catalyst-primary px-5 py-3 text-sm font-semibold text-white opacity-60" role="link" aria-disabled="true" data-link-todo="summit-pass">Get Summit Pass</span><a class="inline-flex min-h-12 items-center justify-center border border-catalyst-primary px-5 py-3 text-sm font-semibold text-catalyst-primary transition-colors duration-200 hover:bg-catalyst-surface" href="#about-summit">View Summit details</a></div></div>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="summit-pass-title">
            <x-ui.container>
                <div class="grid gap-12 lg:grid-cols-12 lg:items-center lg:gap-16">
                    <div class="lg:col-span-5" data-reveal><p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-ink"><span class="size-1.5 rotate-45 bg-catalyst-primary" aria-hidden="true"></span>Summit Pass</p><h2 id="summit-pass-title" class="mt-5 max-w-2xl font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">One pass for the Talkshow and Exhibition.</h2><p class="mt-6 max-w-xl text-base leading-7 text-catalyst-muted">Access Catalyst Summit’s public final-day experiences with one pass, including the Talkshow and Innovation Showcase.</p><p class="mt-5 text-sm text-catalyst-ink/70">Competition registration is separate from the Summit Pass.</p></div>
                    <article class="relative overflow-hidden bg-catalyst-ink p-6 text-white sm:p-9 lg:col-span-7" data-reveal><div class="pointer-events-none absolute -right-20 -top-24 size-72 rounded-full bg-catalyst-lime/20 blur-3xl" aria-hidden="true"></div><div class="relative z-10 flex items-start justify-between gap-5 border-b border-white/20 pb-7"><div><p class="text-xs font-semibold uppercase tracking-[0.14em] text-catalyst-lime">Catalyst Summit 2026</p><h3 class="mt-3 font-display text-4xl font-semibold tracking-tight">Summit Pass</h3></div><span class="bg-white/10 px-3 py-2 text-xs font-semibold text-white">Coming soon</span></div><ul class="relative z-10 mt-8 grid gap-4 text-sm text-white/85 sm:grid-cols-3" aria-label="Summit Pass inclusions">@foreach (['Talkshow access', 'Exhibition access', 'QR check-in'] as $included)<li class="flex items-start gap-2"><svg class="mt-0.5 size-4 shrink-0 text-catalyst-lime" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m3 8.5 3 3L13 4.5" /></svg>{{ $included }}</li>@endforeach</ul><div class="relative z-10 mt-10 flex flex-col gap-5 border-t border-white/20 pt-6 sm:flex-row sm:items-end sm:justify-between"><div><p class="text-xs uppercase tracking-[0.14em] text-white/55">Price</p><p class="mt-2 font-display text-2xl font-semibold">To be announced</p></div><span class="inline-flex min-h-12 cursor-not-allowed items-center justify-center bg-white px-5 py-3 text-sm font-semibold text-catalyst-primary opacity-70" role="link" aria-disabled="true" data-link-todo="summit-pass">Get Summit Pass</span></div></article>
                </div>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="timeline-title">
            <x-ui.container>
                <header class="grid gap-7 lg:grid-cols-12 lg:items-end" data-reveal><div class="lg:col-span-8"><p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-ink"><span class="size-1.5 rotate-45 bg-catalyst-primary" aria-hidden="true"></span>Catalyst 2026 Timeline</p><h2 id="timeline-title" class="mt-5 max-w-3xl font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">The complete road to Catalyst Summit.</h2></div><p class="max-w-md text-sm leading-6 text-catalyst-muted lg:col-span-4">Each milestone updates automatically based on the event schedule in Asia/Jakarta.</p></header>
                <ol class="mt-12 space-y-0 lg:hidden" data-reveal>
                    @foreach ($timeline as $item)
                        @php $state = $eventState($item); @endphp
                        <li class="relative border-l border-catalyst-ink/15 pb-9 pl-8 last:pb-0"><span @class(['absolute -left-[6px] top-0 flex size-3 items-center justify-center rounded-full border bg-white', 'border-catalyst-primary' => $state !== 'upcoming', 'border-catalyst-grey' => $state === 'upcoming']) aria-hidden="true">@if ($state === 'completed')<svg class="size-2 text-catalyst-primary" viewBox="0 0 8 8" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m1.5 4 1.6 1.6L6.5 2" /></svg>@elseif ($state === 'ongoing')<span class="size-1.5 rounded-full bg-catalyst-primary"></span>@endif</span><div class="flex flex-wrap items-baseline justify-between gap-3"><p class="font-display text-2xl font-semibold text-catalyst-primary">{{ $item['date'] }}</p><span class="text-[0.6875rem] font-semibold uppercase tracking-[0.12em] text-catalyst-muted">{{ $statusLabel($state) }}</span></div><p class="mt-2 max-w-sm text-sm leading-6 text-catalyst-ink">{{ $item['title'] }}</p></li>
                    @endforeach
                </ol>
                <ol class="home-timeline__grid mt-16 hidden grid-cols-5 lg:grid" data-reveal>
                    @foreach ($timeline as $item)
                        @php $state = $eventState($item); @endphp
                        <li class="relative min-h-44 border-t border-catalyst-ink/15 pr-7 pt-7" data-timeline-state="{{ $state }}"><span @class(['absolute -top-[6px] left-0 flex size-3 items-center justify-center rounded-full border bg-white', 'border-catalyst-primary' => $state !== 'upcoming', 'border-catalyst-grey' => $state === 'upcoming']) aria-hidden="true">@if ($state === 'completed')<svg class="size-2 text-catalyst-primary" viewBox="0 0 8 8" fill="none" stroke="currentColor" stroke-width="1.5"><path d="m1.5 4 1.6 1.6L6.5 2" /></svg>@elseif ($state === 'ongoing')<span class="size-1.5 rounded-full bg-catalyst-primary"></span>@endif</span><p class="font-display text-2xl font-semibold text-catalyst-primary">{{ $item['date'] }}</p><p class="mt-3 max-w-[11rem] text-sm leading-6 text-catalyst-ink">{{ $item['title'] }}</p><span class="mt-4 inline-block text-[0.6875rem] font-semibold uppercase tracking-[0.12em] text-catalyst-muted">{{ $statusLabel($state) }}</span></li>
                    @endforeach
                </ol>
            </x-ui.container>
        </section>

        <section class="bg-[#f3f6e8] py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="guidebooks-title">
            <x-ui.container>
                <header class="grid gap-7 lg:grid-cols-12 lg:items-end" data-reveal><div class="lg:col-span-7"><p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-ink"><span class="size-1.5 rotate-45 bg-catalyst-primary" aria-hidden="true"></span>Resources & Rules</p><h2 id="guidebooks-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">Start with the official guidebook.</h2></div><p class="max-w-xl text-base leading-7 text-catalyst-muted lg:col-span-5">Read the eligibility, competition stages, submission formats, scoring criteria, and detailed schedules before you register.</p></header>
                <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-4" data-reveal>
                    @foreach ($guidebooks as $guidebook)
                        <article class="group flex min-h-[17rem] flex-col border border-catalyst-primary/20 bg-white p-6 transition-transform duration-200 motion-safe:hover:-translate-y-1"><div class="flex items-start justify-between gap-4"><span class="font-display text-2xl font-semibold text-catalyst-primary">{{ $guidebook['code'] }}</span><svg class="size-6 text-catalyst-primary/60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><path d="M5 3.75h10.5L19 7.25v13H5z"/><path d="M15.5 3.75v3.5H19M8.5 12h7M8.5 16h5"/></svg></div><h3 class="mt-8 font-display text-xl font-semibold text-catalyst-ink">{{ $guidebook['title'] }}</h3><p class="mt-3 text-sm leading-6 text-catalyst-muted">{{ $guidebook['description'] }}</p><span class="mt-auto pt-8 text-sm font-semibold text-catalyst-muted" aria-disabled="true" data-link-todo="{{ strtolower($guidebook['code']) }}-guidebook">Coming soon</span></article>
                    @endforeach
                </div>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="benefits-title">
            <x-ui.container>
                <div class="grid gap-12 lg:grid-cols-12 lg:gap-16"><header class="lg:col-span-5" data-reveal><p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-ink"><span class="size-1.5 rotate-45 bg-catalyst-primary" aria-hidden="true"></span>Why Join</p><h2 id="benefits-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">More than the final result.</h2><p class="mt-6 max-w-lg text-base leading-7 text-catalyst-muted">Catalyst combines real-world challenges, mentoring, exposure, and national connections throughout the competition journey.</p></header><ol class="border-t border-catalyst-ink/15 lg:col-span-7" data-reveal>@foreach ($benefits as $benefit)<li class="grid grid-cols-[3rem_1fr] gap-4 border-b border-catalyst-ink/15 py-6 sm:grid-cols-[4rem_12rem_1fr] sm:gap-6"><span class="font-display text-xl font-semibold text-catalyst-primary">{{ $benefit['number'] }}</span><h3 class="font-display text-xl font-semibold text-catalyst-ink">{{ $benefit['title'] }}</h3><p class="col-start-2 text-sm leading-6 text-catalyst-muted sm:col-start-auto">{{ $benefit['description'] }}</p></li>@endforeach</ol></div>
            </x-ui.container>
        </section>

        <section class="bg-catalyst-neutral py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="people-title">
            <x-ui.container><header class="max-w-3xl" data-reveal><p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-ink"><span class="size-1.5 rotate-45 bg-catalyst-primary" aria-hidden="true"></span>People of Catalyst Summit</p><h2 id="people-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">Meet the people behind the conversations and competitions.</h2><p class="mt-5 text-base leading-7 text-catalyst-muted">Speakers, judges, mentors, and practitioners across Catalyst Summit.</p></header></x-ui.container>
            <div class="home-people-marquee mt-12 overflow-hidden" data-reveal><div class="home-people-marquee__track flex w-max">@foreach ([false, true] as $duplicate)<div class="home-people-marquee__group flex shrink-0 gap-4 pr-4" @if ($duplicate) aria-hidden="true" @endif>@foreach ($people as $person)<article class="home-people-card group w-[17rem] shrink-0 bg-white transition-transform duration-200 motion-safe:hover:-translate-y-1 sm:w-[19rem]"><div class="flex aspect-[4/5] items-center justify-center overflow-hidden bg-catalyst-surface" aria-hidden="true"><svg class="size-20 text-catalyst-primary/30 transition-transform duration-300 group-hover:scale-105" viewBox="0 0 80 80" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="40" cy="29" r="13"/><path d="M17 67c2-14 10-21 23-21s21 7 23 21"/></svg></div><div class="p-5"><p class="text-xs font-semibold uppercase tracking-[0.14em] text-catalyst-primary">{{ $person['role'] }}</p><h3 class="mt-3 font-display text-xl font-semibold text-catalyst-ink">{{ $person['name'] }}</h3><p class="mt-2 text-sm text-catalyst-muted">{{ $person['organization'] }}</p></div></article>@endforeach</div>@endforeach</div></div>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="partners-title">
            <x-ui.container>
                <div class="text-center" data-reveal><p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-ink"><span class="size-1.5 rotate-45 bg-catalyst-primary" aria-hidden="true"></span>Partners behind Catalyst 2026</p><h2 id="partners-title" class="mt-5 font-display text-3xl font-semibold tracking-tight text-catalyst-ink sm:text-4xl">Supported by our strategic partners.</h2></div>
                <div class="mt-10 grid grid-cols-2 border-l border-t border-catalyst-ink/15 sm:grid-cols-3 lg:grid-cols-5" data-reveal>@foreach (range(1, 10) as $partner)<div class="flex min-h-28 items-center justify-center border-b border-r border-catalyst-ink/15 px-5 text-center text-sm font-medium text-catalyst-grey">Partner logo</div>@endforeach</div>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="faq-title">
            <x-ui.container>
                <div class="grid gap-12 lg:grid-cols-12 lg:gap-16"><header class="lg:col-span-4" data-reveal><p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-catalyst-ink"><span class="size-1.5 rotate-45 bg-catalyst-primary" aria-hidden="true"></span>FAQ</p><h2 id="faq-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">Questions<br>before you join?</h2></header><div class="border-t border-catalyst-ink/15 lg:col-span-8" data-reveal>@foreach ($faqs as $faq)<details class="group border-b border-catalyst-ink/15"><summary class="flex min-h-20 list-none items-center justify-between gap-6 py-5 text-left font-display text-lg font-semibold text-catalyst-ink [&::-webkit-details-marker]:hidden"><span>{{ $faq['question'] }}</span><svg class="size-5 shrink-0 text-catalyst-primary transition-transform duration-200 group-open:rotate-45" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M10 4v12M4 10h12" /></svg></summary><p class="max-w-2xl pb-6 pr-10 text-sm leading-7 text-catalyst-muted">{{ $faq['answer'] }}</p></details>@endforeach</div></div>
            </x-ui.container>
        </section>

        <section class="relative overflow-hidden bg-catalyst-primary py-16 text-white sm:py-24 lg:py-28" data-navbar-theme="light" aria-labelledby="final-cta-title">
            <div class="pointer-events-none absolute -left-28 top-1/2 size-72 -translate-y-1/2 rounded-full bg-catalyst-blue/25 blur-3xl" aria-hidden="true"></div><div class="pointer-events-none absolute -right-24 -top-20 size-80 rounded-full bg-catalyst-lime/25 blur-3xl" aria-hidden="true"></div>
            <x-ui.container class="relative z-10"><div class="grid gap-10 lg:grid-cols-12 lg:items-end" data-reveal><div class="lg:col-span-8"><p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.16em] text-white"><span class="size-1.5 rotate-45 bg-catalyst-lime" aria-hidden="true"></span>Join Catalyst Summit</p><h2 id="final-cta-title" class="mt-5 max-w-4xl font-display text-4xl font-semibold leading-tight tracking-tight sm:text-5xl lg:text-6xl">Choose how you’ll be part of the Summit.</h2><p class="mt-6 max-w-2xl text-base leading-7 text-white/80">Compete through MCC, BCC, or BPC, or join the final-day Talkshow and Exhibition with a Summit Pass.</p></div><div class="flex flex-col gap-3 sm:flex-row lg:col-span-4 lg:justify-end"><a class="group inline-flex min-h-12 items-center justify-center gap-2 bg-white px-5 py-3 text-sm font-semibold text-catalyst-primary transition-colors duration-200 hover:bg-catalyst-lime" href="{{ route('competitions.index') }}">Explore competitions<svg class="size-4 transition-transform duration-200 group-hover:translate-x-1" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M3 8h9M8.5 4.5 12 8l-3.5 3.5" /></svg></a><span class="inline-flex min-h-12 cursor-not-allowed items-center justify-center border border-white/70 px-5 py-3 text-sm font-semibold text-white opacity-70" role="link" aria-disabled="true" data-link-todo="summit-pass">Get Summit Pass</span></div></div></x-ui.container>
        </section>
    </div>
@endsection
