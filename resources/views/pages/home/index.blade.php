@extends('layouts.public')

@section('title', 'Home')

@section('content')
    @php
        $guidebookUrl = config('services.catalyst.guidebook_url');
        $nowWib = \Carbon\CarbonImmutable::now('Asia/Jakarta');

        $schedule = [
            'green-action' => ['start' => '2026-09-13 00:00:00', 'end' => '2026-09-13 23:59:59', 'name' => 'Green Action', 'description' => 'Confirmed Catalyst pre-event milestone.'],
            'mentorship-registration-opens' => ['start' => '2026-09-20 00:00:00', 'end' => '2026-09-20 23:59:59', 'name' => 'Mentorship Registration Opens', 'description' => 'Confirmed Catalyst pre-event milestone.'],
            'mcc-registration-opens' => ['start' => '2026-09-27 00:00:00', 'end' => '2026-09-27 23:59:59', 'name' => 'MCC Registration Opens', 'description' => 'Registration for the Mini Case Competition officially opens.'],
            'mcc-early-bird' => ['start' => '2026-09-27 00:00:00', 'end' => '2026-10-03 23:59:59', 'name' => 'MCC Early Bird Registration'],
            'mcc-normal-registration' => ['start' => '2026-10-04 00:00:00', 'end' => '2026-10-09 23:59:59', 'name' => 'MCC Normal Registration'],
            'mcc-case-release' => ['start' => '2026-10-05 00:00:00', 'end' => '2026-10-05 23:59:59', 'name' => 'MCC Case Release'],
            'mcc-late-bird' => ['start' => '2026-10-10 00:00:00', 'end' => '2026-10-10 23:59:59', 'name' => 'MCC Late Bird Registration & Technical Meeting'],
            'mcc-submission-deadline' => ['start' => '2026-10-17 00:00:00', 'end' => '2026-10-17 23:59:59', 'name' => 'MCC Submission Deadline'],
            'mcc-selection' => ['start' => '2026-10-21 00:00:00', 'end' => '2026-10-21 23:59:59', 'name' => 'MCC Selection Process'],
            'mcc-winner-announcement' => ['start' => '2026-10-24 00:00:00', 'end' => '2026-10-24 23:59:59', 'name' => 'MCC Winner Announcement'],
            'bcc-bpc-normal-registration' => ['start' => '2026-10-11 00:00:00', 'end' => '2026-10-24 23:59:59', 'name' => 'BCC & BPC Normal Registration'],
            'bcc-bpc-late-registration' => ['start' => '2026-10-25 00:00:00', 'end' => '2026-10-31 23:59:59', 'name' => 'BCC & BPC Late Bird Registration'],
            'bcc-bpc-registration-closes' => ['start' => '2026-10-31 00:00:00', 'end' => '2026-10-31 23:59:59', 'name' => 'BCC & BPC Registration Closes'],
            'bcc-bpc-technical-meeting' => ['start' => '2026-11-01 00:00:00', 'end' => '2026-11-01 23:59:59', 'name' => 'BCC & BPC Technical Meeting & Bootcamp'],
            'preliminary-stage' => ['start' => '2026-11-02 00:00:00', 'end' => '2026-11-04 23:59:59', 'name' => 'BCC & BPC Preliminary Stage'],
            'preliminary-submission-deadline' => ['start' => '2026-11-05 00:00:00', 'end' => '2026-11-05 23:59:59', 'name' => 'Preliminary Submission Deadline'],
            'preliminary-selection' => ['start' => '2026-11-06 00:00:00', 'end' => '2026-11-06 23:59:59', 'name' => 'Preliminary Selection'],
            'semifinalist-announcement' => ['start' => '2026-11-07 00:00:00', 'end' => '2026-11-07 23:59:59', 'name' => 'Semifinalist Announcement'],
            'semifinal-technical-meeting' => ['start' => '2026-11-08 00:00:00', 'end' => '2026-11-08 23:59:59', 'name' => 'Semifinal Technical Meeting'],
            'semifinal-stage' => ['start' => '2026-11-09 00:00:00', 'end' => '2026-11-13 23:59:59', 'name' => 'BCC & BPC Semifinal Stage'],
            'semifinal-submission' => ['start' => '2026-11-14 00:00:00', 'end' => '2026-11-14 23:59:59', 'name' => 'Semifinal Submission Deadline'],
            'semifinal-selection' => ['start' => '2026-11-15 00:00:00', 'end' => '2026-11-20 23:59:59', 'name' => 'Semifinal Selection & Assessment'],
            'finalist-announcement' => ['start' => '2026-11-21 00:00:00', 'end' => '2026-11-21 23:59:59', 'name' => 'Finalist Announcement'],
            'finalist-technical-meeting' => ['start' => '2026-11-22 00:00:00', 'end' => '2026-11-22 23:59:59', 'name' => 'Finalist Technical Meeting'],
            'final-preparation' => ['start' => '2026-11-23 00:00:00', 'end' => '2026-11-26 23:59:59', 'name' => 'Final Presentation Preparation & Submission Period'],
            'final-submission-deadline' => ['start' => '2026-11-27 00:00:00', 'end' => '2026-11-27 23:59:59', 'name' => 'Final Presentation Submission Deadline'],
            'main-event' => ['start' => '2026-11-29 00:00:00', 'end' => '2026-11-29 23:59:59', 'name' => 'Catalyst 2026 Main Event: Final Day', 'description' => 'Final Pitch, Exhibition & Innovation Showcase'],
        ];

        $eventState = function (array $event) use ($nowWib): string {
            $start = \Carbon\CarbonImmutable::parse($event['start'], 'Asia/Jakarta');
            $end = \Carbon\CarbonImmutable::parse($event['end'], 'Asia/Jakarta');

            return match (true) {
                $nowWib->lt($start) => 'upcoming',
                $nowWib->gt($end) => 'completed',
                default => 'ongoing',
            };
        };

        $statusLabel = fn (string $state): string => match ($state) {
            'completed' => 'Completed',
            'ongoing' => 'Ongoing',
            default => 'Upcoming',
        };

        $journey = [
            ['phase' => 'Pre-Event 1', 'name' => 'Catalyst Mentorship Track', 'copy' => 'Intensive mentoring for Business Case and Business Plan teams in renewable energy.', 'event' => 'mentorship-registration-opens'],
            ['phase' => 'Pre-Event 2', 'name' => 'Catalyst Green Action', 'copy' => 'Mangrove planting and renewable energy education through hands-on environmental action.', 'event' => 'green-action'],
            ['phase' => 'Main Event', 'name' => 'Catalyst Summit', 'copy' => 'MCC, BCC, BPC, final pitches, talkshow, and the Exhibition & Innovation Showcase.', 'event' => 'main-event'],
        ];

        $registrationState = function (string $startAt, string $endAt) use ($nowWib): string {
            $start = \Carbon\CarbonImmutable::parse($startAt, 'Asia/Jakarta');
            $end = \Carbon\CarbonImmutable::parse($endAt, 'Asia/Jakarta');

            return match (true) {
                $nowWib->lt($start) => 'upcoming',
                $nowWib->gt($end) => 'closed',
                default => 'registration_open',
            };
        };

        $competitions = [
            ['code' => 'MCC', 'name' => 'Mini Case Competition', 'audience' => 'High School Students', 'members' => '3 Members', 'prize' => 'IDR 2M+ Prize Pool', 'copy' => 'Solve a mini-case on energy transition and business issues through structured analysis and a concise presentation deck.', 'registration_start' => $schedule['mcc-registration-opens']['start'], 'registration_end' => $schedule['mcc-late-bird']['end']],
            ['code' => 'BCC', 'name' => 'Business Case Competition', 'audience' => 'University Students', 'members' => '3 Members', 'prize' => 'IDR 9.5M Prize Pool', 'copy' => 'Tackle renewable-energy deployment challenges in Eastern Indonesia from initial analysis to final pitch.', 'registration_start' => $schedule['bcc-bpc-normal-registration']['start'], 'registration_end' => $schedule['bcc-bpc-registration-closes']['end']],
            ['code' => 'BPC', 'name' => 'Business Plan Competition', 'audience' => 'University Students', 'members' => '3 Members', 'prize' => 'IDR 5M Prize Pool', 'copy' => 'Build a resilient, inclusive, carbon-conscious venture from Business Model Canvas to proposal and prototype.', 'registration_start' => $schedule['bcc-bpc-normal-registration']['start'], 'registration_end' => $schedule['bcc-bpc-registration-closes']['end']],
        ];

        $timeline = [
            ['date' => '13 Sep', 'label' => 'Green Action', 'event' => 'green-action'],
            ['date' => '20 Sep', 'label' => 'Mentorship Registration Opens', 'event' => 'mentorship-registration-opens'],
            ['date' => '27 Sep', 'label' => 'MCC Registration Opens', 'event' => 'mcc-registration-opens'],
            ['date' => '17 Oct', 'label' => 'MCC Submission Deadline', 'event' => 'mcc-submission-deadline'],
            ['date' => '24 Oct', 'label' => 'MCC Winner Announcement', 'event' => 'mcc-winner-announcement'],
            ['date' => '11 Oct', 'label' => 'BCC & BPC Registration Opens', 'event' => 'bcc-bpc-normal-registration'],
            ['date' => '31 Oct', 'label' => 'BCC & BPC Registration Closes', 'event' => 'bcc-bpc-registration-closes'],
            ['date' => '14 Nov', 'label' => 'Semifinal Submission', 'event' => 'semifinal-submission'],
            ['date' => '21 Nov', 'label' => 'Finalist Announcement', 'event' => 'finalist-announcement'],
            ['date' => '29 Nov', 'label' => 'Final Pitch & Exhibition', 'event' => 'main-event'],
        ];

        $horizonValues = [
            ['01', 'Resilient', 'Build solutions that can adapt to real economic, social, and environmental challenges.', 'images/icon/resilient-icon-whyhorizon.svg'],
            ['02', 'Inclusive', 'Make the energy transition accessible and valuable across different communities.', 'images/icon/inclusive-icon-whyhorizon.svg'],
            ['03', 'Zero-Carbon', 'Turn innovation into practical strategies that reduce emissions and support a cleaner energy future.', 'images/icon/zerocarbon-icon-whyhorizon.svg'],
        ];

        $benefits = [
            ['title' => 'Direct Mentorship', 'copy' => 'Refine business cases and business plans with experienced mentors.', 'icon' => 'images/icon/mentorship-icon-whyparticipate.svg'],
            ['title' => 'Real-World Challenges', 'copy' => 'Work on renewable-energy and sustainability problems grounded in real contexts.', 'icon' => 'images/icon/realworld-icon-whyparticipate.svg'],
            ['title' => 'Exhibition Exposure', 'copy' => 'Selected teams can showcase ideas, posters, and prototypes to a wider audience.', 'icon' => 'images/icon/exposure-icon-whyparticipate.svg'],
            ['title' => 'National Connections', 'copy' => 'Meet students, mentors, practitioners, industry players, and stakeholders.', 'icon' => 'images/icon/connection-icon-whyparticipate.svg'],
            ['title' => 'Recognition', 'copy' => 'Earn e-certificates and recognition throughout the Catalyst journey.', 'icon' => 'images/icon/recognition-icon-whyparticipate.svg'],
            ['title' => 'IDR 16.5M+ Prize Pool', 'copy' => 'Compete across MCC, BCC, and BPC, with additional advancement opportunities.', 'icon' => 'images/icon/prizepool-icon-whyparticipate.svg'],
        ];

        $guidebooks = [
            ['code' => 'MCC', 'name' => 'Mini Case Competition'],
            ['code' => 'BCC', 'name' => 'Business Case Competition'],
            ['code' => 'BPC', 'name' => 'Business Plan Competition'],
        ];

        $people = [
            ['role' => 'Talkshow Speaker', 'name' => 'To Be Announced'],
            ['role' => 'Competition Judge', 'name' => 'To Be Announced'],
            ['role' => 'Industry Mentor', 'name' => 'To Be Announced'],
            ['role' => 'Energy Practitioner', 'name' => 'To Be Announced'],
            ['role' => 'Startup Founder', 'name' => 'To Be Announced'],
            ['role' => 'Sustainability Expert', 'name' => 'To Be Announced'],
            ['role' => 'Academic Advisor', 'name' => 'To Be Announced'],
            ['role' => 'More Announcements', 'name' => 'Coming Soon'],
        ];
    @endphp

    <div class="home-page overflow-clip">
        <section class="bg-white" data-navbar-theme="dark" aria-labelledby="home-hero-title">
            <x-ui.container class="pb-0 pt-12 sm:pt-16 lg:pt-24">
                <div class="text-sm text-catalyst-muted sm:text-base">
                    <a class="inline-flex border-b border-catalyst-grey/50 pb-2 hover:border-catalyst-primary hover:text-catalyst-primary" href="#guidebook">
                        Get the guidebook when it lands
                    </a>
                </div>

                <div class="grid gap-10 py-14 sm:py-20 lg:grid-cols-12 lg:items-end lg:gap-8 lg:py-24">
                    <h1 id="home-hero-title" class="font-display text-6xl font-semibold leading-[0.88] tracking-[-0.055em] text-catalyst-black sm:text-7xl md:text-8xl lg:col-span-7 lg:text-[6rem]">
                        Catalyst<br><span class="text-catalyst-primary">Summit</span>
                    </h1>
                    <p class="max-w-xl text-lg leading-8 text-catalyst-ink lg:col-span-5 lg:justify-self-end lg:text-xl">
                        Indonesia’s national innovation platform for young changemakers in renewable energy.
                        <span class="text-catalyst-muted">Learn from mentors, tackle real challenges, and turn ideas into applicable solutions.</span>
                    </p>
                </div>
            </x-ui.container>

            <figure class="relative h-72 overflow-hidden sm:h-96 lg:h-[26.25rem]" data-navbar-theme="light">
                <img class="size-full origin-bottom scale-125 object-cover object-bottom sm:scale-110 lg:scale-100" src="{{ asset('images/brand/footer-image.webp') }}" width="1440" height="700" fetchpriority="high" decoding="async" alt="Solar panels beneath an open sky">
                <div class="pointer-events-none absolute inset-0 bg-gradient-to-b from-catalyst-ink/5 via-transparent to-catalyst-ink/20" aria-hidden="true"></div>
            </figure>
        </section>

        <section class="bg-white py-16 sm:py-20 lg:py-24" data-navbar-theme="dark" aria-labelledby="partners-title">
            <x-ui.container>
                <header class="mx-auto max-w-2xl text-center" data-reveal>
                    <x-public.section-label class="justify-center">Supported by</x-public.section-label>
                    <h2 id="partners-title" class="mt-5 font-display text-3xl font-semibold tracking-tight text-catalyst-ink sm:text-4xl">Partners behind Catalyst Summit 2026.</h2>
                </header>

                <div class="mt-10 grid grid-cols-2 border-l border-t border-catalyst-grey/30 sm:grid-cols-3 lg:mt-12 lg:grid-cols-5" aria-label="Partner logos to be announced" data-reveal>
                    @foreach (range(1, 10) as $partner)
                        <div class="flex min-h-24 items-center justify-center border-b border-r border-catalyst-grey/30 px-4 py-6 text-center text-xs font-semibold uppercase tracking-[0.14em] text-catalyst-grey sm:min-h-28">Partner logo</div>
                    @endforeach
                </div>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="about-title">
            <x-ui.container>
                <x-public.section-label>About catalyst</x-public.section-label>
                <div class="mt-6 grid gap-16 lg:grid-cols-12 lg:gap-10" data-reveal>
                    <div class="lg:col-span-7">
                        <h2 id="about-title" class="max-w-2xl font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">More than one event. <span class="text-catalyst-grey">One connected journey.</span></h2>
                        <p class="mt-16 max-w-xl text-base leading-7 text-catalyst-ink sm:mt-32 sm:text-lg sm:leading-8">Catalyst Summit connects students, mentors, practitioners, industry players, and stakeholders through pre-events, mentorship, competitions, and a final showcase, turning ideas into applicable solutions for Indonesia’s energy transition.</p>
                    </div>

                    <dl class="lg:col-span-5">
                        @foreach ([['03', 'Event Phases'], ['03', 'Competition Tracks'], ['01', 'Catalyst Summit']] as [$value, $label])
                            <div class="home-about-stat grid grid-cols-[5rem_minmax(0,10rem)] items-end justify-end gap-5 py-7 sm:grid-cols-[6rem_minmax(0,11rem)]">
                                <dd class="font-display text-5xl font-semibold tracking-tight text-catalyst-primary sm:text-6xl">{{ $value }}</dd>
                                <dt class="pb-1 text-right font-display text-lg font-semibold text-catalyst-ink sm:text-xl">{{ $label }}</dt>
                            </div>
                        @endforeach
                    </dl>
                </div>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="theme-title">
            <x-ui.container class="text-center">
                <div data-reveal>
                    <x-public.section-label class="justify-center">Grand theme</x-public.section-label>
                    <h2 id="theme-title" class="mx-auto mt-5 max-w-2xl font-display text-xl font-semibold leading-7 text-catalyst-ink sm:text-2xl">Harnessing Opportunities for Resilient,<br class="hidden sm:block"> Inclusive, Zero-Carbon Outcomes<br class="hidden sm:block"> Through Networks</h2>
                </div>

                <div class="home-horizon relative -mx-5 my-14 overflow-hidden py-8 sm:-mx-6 sm:my-20 lg:-mx-8" aria-label="HORIZON">
                    <p class="home-horizon__word relative whitespace-nowrap font-display leading-[0.76] text-catalyst-primary" aria-hidden="true">
                        @foreach (str_split('HORIZON') as $character)
                            <span>{{ $character }}</span>
                        @endforeach
                    </p>
                    <span class="home-horizon__light home-horizon__light--left" aria-hidden="true"></span>
                    <span class="home-horizon__light home-horizon__light--right" aria-hidden="true"></span>
                </div>

                <p class="mx-auto max-w-3xl text-base leading-7 text-catalyst-ink sm:text-lg sm:leading-8" data-reveal>HORIZON brings resilience, inclusion, zero-carbon thinking, and collaboration into one shared direction. It challenges young innovators to connect ideas, technology, and networks into solutions built for long-term impact.</p>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="horizon-values-title">
            <x-ui.container>
                <header class="max-w-2xl" data-reveal>
                    <x-public.section-label>Why Horizon</x-public.section-label>
                    <h2 id="horizon-values-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">The challenges ahead are connected.</h2>
                    <p class="mt-4 text-lg text-catalyst-ink sm:text-xl">So the solutions need to be connected too.</p>
                </header>

                <div class="mt-14 grid md:grid-cols-3" data-reveal>
                    @foreach ($horizonValues as [$number, $title, $copy, $icon])
                        <article class="border-b border-catalyst-primary/30 py-8 last:border-b-0 md:border-b-0 md:border-r md:px-8 md:py-10 md:first:pl-0 md:last:border-r-0 md:last:pr-0">
                            <div class="flex items-start justify-between gap-8">
                                <img class="size-8" src="{{ asset($icon) }}" width="32" height="32" decoding="async" alt="">
                                <p class="text-sm font-semibold text-catalyst-primary">{{ $number }}</p>
                            </div>
                            <h3 class="mt-8 font-display text-2xl font-semibold text-catalyst-ink">{{ $title }}</h3>
                            <p class="mt-5 max-w-sm text-base leading-7 text-catalyst-muted">{{ $copy }}</p>
                        </article>
                    @endforeach
                </div>
            </x-ui.container>
        </section>

        <section class="bg-catalyst-surface py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="journey-title">
            <x-ui.container>
                <header class="max-w-3xl" data-reveal>
                    <x-public.section-label>The Catalyst journey</x-public.section-label>
                    <h2 id="journey-title" class="mt-5 font-display text-4xl font-semibold tracking-tight text-catalyst-ink sm:text-5xl">Three phases. One connected journey.</h2>
                    <p class="mt-4 text-lg leading-8 text-catalyst-ink sm:text-xl">From guided learning and hands-on action to a national competition stage.</p>
                </header>

                <div class="mt-12 grid gap-5 md:grid-cols-3 lg:mt-20" data-reveal>
                    @foreach ($journey as $item)
                        @php($state = $eventState($schedule[$item['event']]))
                        <article class="group flex min-h-[24rem] flex-col bg-white p-6 transition-transform duration-200 motion-safe:hover:-translate-y-1 sm:p-7" data-event-state="{{ $state }}">
                            <span @class([
                                'self-start border px-3 py-2 text-sm font-medium',
                                'border-catalyst-green/30 bg-catalyst-green/15 text-status-success-ink' => $state === 'completed',
                                'border-catalyst-lime/60 bg-catalyst-lime/30 text-catalyst-ink' => $state === 'ongoing',
                                'border-catalyst-grey/30 bg-catalyst-grey/10 text-catalyst-muted' => $state === 'upcoming',
                            ])>{{ $statusLabel($state) }}</span>
                            <p class="mt-7 font-display text-2xl font-semibold text-catalyst-ink">{{ $item['phase'] }}</p>
                            <h3 class="mt-2 text-sm font-semibold tracking-[0.08em] text-catalyst-primary">{{ $item['name'] }}</h3>
                            <p class="mt-auto pt-16 text-base leading-7 text-catalyst-ink">{{ $item['copy'] }}</p>
                        </article>
                    @endforeach
                </div>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="competitions-title">
            <x-ui.container>
                <header class="max-w-2xl" data-reveal>
                    <x-public.section-label>Compete at Catalyst</x-public.section-label>
                    <h2 id="competitions-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">Three competitions.<br>Different ways to solve.</h2>
                    <p class="mt-4 text-lg leading-8 text-catalyst-ink sm:text-xl">Choose the challenge that matches your background, strengths, and way of thinking.</p>
                </header>

                <div class="mt-12 grid gap-5 lg:mt-20 lg:grid-cols-3" data-reveal>
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
                                <a class="mt-5 inline-flex items-center gap-2 text-sm font-semibold text-catalyst-primary" href="{{ route('competitions.index') }}">Explore {{ $competition['code'] }}<x-public.link-arrow /></a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="timeline-title">
            <x-ui.container>
                <header class="max-w-2xl" data-reveal>
                    <x-public.section-label>Catalyst 2026 timeline</x-public.section-label>
                    <h2 id="timeline-title" class="mt-5 font-display text-4xl font-semibold tracking-tight text-catalyst-ink sm:text-5xl">The journey at a glance.</h2>
                </header>

                <ol class="relative mt-14 border-l border-dashed border-catalyst-primary/35 pl-9 lg:hidden" data-reveal>
                    @foreach ($timeline as $milestone)
                        @php($state = $eventState($schedule[$milestone['event']]))
                        <li class="relative pb-10 last:pb-0" data-timeline-state="{{ $state }}">
                            <span class="absolute -left-[3.28rem] -top-1 grid size-8 place-items-center bg-white" aria-hidden="true">
                                @if ($state === 'completed')
                                    <img class="size-8" src="{{ asset('images/icon/completed-icon-timeline.svg') }}" width="32" height="32" alt="">
                                @elseif ($state === 'ongoing')
                                    <img class="size-8" src="{{ asset('images/icon/ongoing-icon-timeline.svg') }}" width="32" height="32" alt="">
                                @else
                                    <span class="size-5 rounded-full border border-catalyst-primary/45 bg-white"></span>
                                @endif
                            </span>
                            <time class="font-display text-xl font-semibold text-catalyst-ink sm:text-2xl">{{ $milestone['date'] }}</time>
                            <p class="mt-2 max-w-xs text-sm leading-5 text-catalyst-muted sm:text-base">{{ $milestone['label'] }}</p>
                        </li>
                    @endforeach
                </ol>

                <div class="relative mt-20 hidden lg:block" data-reveal>
                    <span class="home-timeline__turn" aria-hidden="true"></span>
                    <ol class="home-timeline__grid grid" aria-label="Catalyst 2026 milestones">
                        @foreach ($timeline as $milestone)
                            @php($state = $eventState($schedule[$milestone['event']]))
                            <li class="relative z-10 pt-12" data-timeline-state="{{ $state }}">
                                <span class="absolute left-0 top-0 grid size-8 place-items-center bg-white" aria-hidden="true">
                                    @if ($state === 'completed')
                                        <img class="size-8" src="{{ asset('images/icon/completed-icon-timeline.svg') }}" width="32" height="32" alt="">
                                    @elseif ($state === 'ongoing')
                                        <img class="size-8" src="{{ asset('images/icon/ongoing-icon-timeline.svg') }}" width="32" height="32" alt="">
                                    @else
                                        <span class="size-5 rounded-full border border-catalyst-primary/45 bg-white"></span>
                                    @endif
                                </span>
                                <time class="font-display text-xl font-semibold text-catalyst-ink sm:text-2xl">{{ $milestone['date'] }}</time>
                                <p class="mt-3 max-w-[11rem] text-sm leading-5 text-catalyst-muted sm:text-base">{{ $milestone['label'] }}</p>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="benefits-title">
            <x-ui.container>
                <header class="max-w-3xl" data-reveal>
                    <x-public.section-label>Why participate</x-public.section-label>
                    <h2 id="benefits-title" class="mt-5 font-display text-4xl font-semibold tracking-tight text-catalyst-ink sm:text-5xl">More than the final result.</h2>
                    <p class="mt-4 text-lg leading-8 text-catalyst-ink sm:text-xl">Catalyst combines mentoring, competition, real-world exposure, and national connections throughout the journey.</p>
                </header>

                <div class="mt-12 grid gap-3 sm:grid-cols-2 lg:mt-20 lg:grid-cols-3" data-reveal>
                    @foreach ($benefits as $benefit)
                        <article class="min-h-52 bg-catalyst-neutral p-6 sm:p-7">
                            <img class="size-6" src="{{ asset($benefit['icon']) }}" width="24" height="24" decoding="async" alt="">
                            <h3 class="mt-5 font-display text-xl font-semibold text-catalyst-ink">{{ $benefit['title'] }}</h3>
                            <p class="mt-3 max-w-sm text-sm leading-6 text-catalyst-muted sm:text-base">{{ $benefit['copy'] }}</p>
                        </article>
                    @endforeach
                </div>
            </x-ui.container>
        </section>

        <section id="guidebook" class="scroll-mt-32 bg-catalyst-lime/10 py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="resources-title">
            <x-ui.container>
                <header class="max-w-3xl" data-reveal>
                    <x-public.section-label>Resources</x-public.section-label>
                    <h2 id="resources-title" class="mt-5 font-display text-4xl font-semibold tracking-tight text-catalyst-ink sm:text-5xl">Start with the official guidebook.</h2>
                    <p class="mt-4 text-lg leading-8 text-catalyst-ink sm:text-xl">Competition rules, eligibility, stages, submissions, and scoring, all in one place.</p>
                </header>

                <div class="mt-12 grid gap-5 md:grid-cols-3 lg:mt-20" data-reveal>
                    @foreach ($guidebooks as $guidebook)
                        <article class="flex min-h-64 flex-col bg-white p-6 sm:p-7">
                            <img class="size-6" src="{{ asset('images/icon/guidebook-icon-resource.svg') }}" width="24" height="24" decoding="async" alt="">
                            <h3 class="mt-5 font-display text-xl font-semibold text-catalyst-ink">{{ $guidebook['code'] }} Guidebook</h3>
                            <p class="mt-2 text-sm text-catalyst-muted">{{ $guidebook['name'] }}</p>
                            <a
                                class="mt-auto inline-flex items-center gap-2 self-start pt-8 text-base text-catalyst-primary"
                                href="{{ $guidebookUrl ?: '#guidebook' }}"
                                @if ($guidebookUrl) target="_blank" rel="noopener noreferrer" @else aria-disabled="true" data-link-todo="{{ strtolower($guidebook['code']) }}-guidebook" title="Guidebook URL to be confirmed" @endif
                            >Open guidebook<x-public.link-arrow external /></a>
                        </article>
                    @endforeach
                </div>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="people-title">
            <x-ui.container>
                <header class="max-w-4xl" data-reveal>
                    <x-public.section-label>People of Catalyst</x-public.section-label>
                    <h2 id="people-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">Meet the people <span class="text-catalyst-grey">behind the mentoring, judging, and conversations.</span></h2>
                    <p class="mt-4 text-lg leading-8 text-catalyst-ink sm:text-xl">Mentors, judges, practitioners, and speakers across the Catalyst journey.</p>
                </header>
            </x-ui.container>

            <div class="home-people-marquee mt-12 lg:mt-20" aria-label="People of Catalyst announcements">
                <div class="home-people-marquee__track">
                    @foreach ([false, true] as $duplicate)
                        <div class="home-people-marquee__group" @if ($duplicate) aria-hidden="true" @endif>
                            @foreach ($people as $person)
                                <article class="home-people-card">
                                    <div class="grid aspect-[3/4] place-items-center border border-catalyst-primary/20 bg-catalyst-surface p-5 text-center">
                                        <div>
                                            <img class="mx-auto h-16 w-auto opacity-70" src="{{ asset('images/brand/catalyst-mark.png') }}" width="35" height="64" loading="lazy" decoding="async" alt="">
                                            <p class="mt-4 text-xs font-medium leading-5 text-catalyst-primary">Official announcement<br>coming soon</p>
                                        </div>
                                    </div>
                                    <p class="mt-4 text-xs font-semibold uppercase tracking-[0.14em] text-catalyst-primary">{{ $person['role'] }}</p>
                                    <h3 class="mt-2 font-display text-sm font-semibold text-catalyst-ink sm:text-base">{{ $person['name'] }}</h3>
                                </article>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="bg-white py-10 sm:py-14" data-navbar-theme="dark" aria-labelledby="partnership-title">
            <x-ui.container>
                <div class="home-partnership relative isolate overflow-hidden" data-reveal>
                    <img class="absolute inset-0 -z-20 size-full object-cover object-center" src="{{ asset('images/brand/footer-image.webp') }}" width="1440" height="700" loading="lazy" decoding="async" alt="">
                    <div class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-r from-white via-white/95 to-white/65 sm:to-white/45 lg:to-white/10" aria-hidden="true"></div>
                    <div class="pointer-events-none absolute inset-0 -z-10 bg-gradient-to-b from-white/35 via-transparent to-white/30" aria-hidden="true"></div>
                    <div class="relative max-w-3xl p-7 sm:p-10 lg:p-14">
                        <x-public.section-label>Partner with Catalyst</x-public.section-label>
                        <h2 id="partnership-title" class="mt-5 font-display text-3xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-4xl">Interested in collaborating with Catalyst Summit 2026?</h2>
                        <p class="mt-4 max-w-2xl text-base leading-7 text-catalyst-muted">Partner with a national platform connecting young innovators, mentors, industry players, practitioners, and stakeholders in renewable energy.</p>
                        <div class="mt-8 flex flex-col items-start gap-4 sm:flex-row sm:items-center">
                            <span class="inline-flex min-h-12 items-center gap-2 bg-catalyst-primary px-5 py-3 text-sm font-semibold text-white opacity-60" role="link" aria-disabled="true" data-link-todo="partnership-contact">
                                Explore Partnership Opportunities
                                <img class="size-4 brightness-0 invert" src="{{ asset('images/icon/arrow-icon-diagonal.svg') }}" width="16" height="16" alt="">
                            </span>
                            <p class="text-sm text-catalyst-muted">Partnership contact destination is being finalized.</p>
                        </div>
                    </div>
                </div>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="faq-title">
            <x-ui.container>
                <x-public.section-label>FAQ</x-public.section-label>
                <div class="mt-6 grid gap-12 lg:grid-cols-12 lg:gap-16" data-reveal>
                    <div class="lg:col-span-4">
                        <h2 id="faq-title" class="font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">Questions<br>before you join?</h2>
                        {{-- TODO: Replace this clearly marked prototype contact with the confirmed Catalyst contact person. --}}
                        <div class="mt-12 text-sm leading-6 text-catalyst-muted sm:mt-28" data-content-todo="contact-person">
                            <p class="font-semibold text-catalyst-ink">Contact person</p>
                            <p class="mt-2">Alya Putri</p>
                            <p>+62 812-3456-7890</p>
                            <p class="mt-2 text-xs">Prototype contact — replace before launch.</p>
                        </div>
                    </div>
                    <div class="lg:col-span-8">
                        <details class="group border-b border-catalyst-grey/30 py-5" open>
                            <summary class="flex min-h-11 list-none items-center justify-between gap-6 font-display text-lg font-medium text-catalyst-ink [&::-webkit-details-marker]:hidden sm:text-xl">
                                What is Catalyst 2026?
                                <span class="relative size-4 shrink-0" aria-hidden="true"><span class="absolute left-0 top-1/2 h-px w-4 bg-current"></span><span class="absolute left-1/2 top-0 h-4 w-px bg-current transition-transform group-open:rotate-90 group-open:opacity-0"></span></span>
                            </summary>
                            <p class="max-w-2xl pb-2 pr-8 pt-4 text-base leading-7 text-catalyst-muted">Catalyst 2026 is a connected journey of pre-events, mentorship, competitions, and a final showcase for young changemakers in renewable energy.</p>
                        </details>
                        <div class="border-b border-catalyst-grey/30 py-5 text-base text-catalyst-grey" data-content-todo="faq">More frequently asked questions are coming soon.</div>
                    </div>
                </div>
            </x-ui.container>
        </section>
    </div>
@endsection
