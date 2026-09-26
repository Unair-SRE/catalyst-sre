@extends('layouts.public')

@section('title', 'Catalyst Mentorship Track — Catalyst 2026')

@section('content')
    @php
        $guidebookUrl = config('services.catalyst.mentorship_guidebook_url');

        $experiences = [
            ['number' => '01', 'title' => 'Ignition & Track Splitting', 'copy' => 'Build the foundation, enter your selected track, and begin developing your business case or business plan.'],
            ['number' => '02', 'title' => 'Acceleration Lab', 'copy' => 'Align the idea, sharpen the presentation, and prepare a clear roadmap for the mentoring phase ahead.'],
            ['number' => '03', 'title' => 'Mentoring to Final Pitch', 'copy' => 'Refine the work through mentor assistance and discussion, then present the final idea before the panel.'],
        ];

        $timeline = [
            ['20–27 Sep', 'Registration', '2026-09-20 00:00:00', '2026-09-27 23:59:59'],
            ['3 Oct', 'Ignition & Track Splitting', '2026-10-03 00:00:00', '2026-10-03 23:59:59'],
            ['4 Oct', 'Acceleration Lab', '2026-10-04 00:00:00', '2026-10-04 23:59:59'],
            ['5–16 Oct', 'Mentoring Phase', '2026-10-05 00:00:00', '2026-10-16 23:59:59'],
            ['17 Oct', 'Final Pitching Day', '2026-10-17 00:00:00', '2026-10-17 23:59:59'],
        ];

        $now = \Carbon\CarbonImmutable::now('Asia/Jakarta');
        $timelineState = static function (string $start, string $end) use ($now): string {
            $startsAt = \Carbon\CarbonImmutable::parse($start, 'Asia/Jakarta');
            $endsAt = \Carbon\CarbonImmutable::parse($end, 'Asia/Jakarta');

            return match (true) {
                $now->isAfter($endsAt) => 'completed',
                $now->betweenIncluded($startsAt, $endsAt) => 'ongoing',
                default => 'upcoming',
            };
        };

        $people = [
            ['Mentor', 'Business Case Mentor'],
            ['Judge', 'Final Pitch Judge'],
            ['Mentor', 'Business Plan Mentor'],
            ['Judge', 'Final Pitch Judge'],
            ['Mentor', 'Business Case Mentor'],
            ['Judge', 'Final Pitch Judge'],
            ['Mentor', 'Business Plan Mentor'],
            ['Judge', 'Final Pitch Judge'],
        ];

        $highlightImages = [
            ['images/brand/footer-image.webp', 'object-left'],
            ['images/brand/footer-image.webp', 'object-center'],
            ['images/brand/footer-image.webp', 'object-right'],
        ];
    @endphp

    <div class="home-page overflow-clip" data-pre-event-one-page>
        <section
            class="relative -mt-24 flex min-h-[42rem] items-end overflow-hidden bg-catalyst-ink pb-8 pt-36 text-white sm:-mt-28 sm:min-h-[46rem] sm:pb-12 lg:-mt-36 lg:min-h-[52rem] lg:pb-14"
            data-navbar-theme="light"
            aria-labelledby="pre-event-one-hero-title"
        >
            <img class="absolute inset-0 size-full scale-105 object-cover object-center" src="{{ asset('images/brand/footer-image.webp') }}" width="1440" height="700" fetchpriority="high" decoding="async" alt="">
            <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(23,33,38,.18)_0%,rgba(23,33,38,.38)_42%,rgba(10,26,27,.92)_100%)]" aria-hidden="true"></div>

            <x-ui.container class="relative z-10 w-full">
                <div class="grid gap-8 border-b border-white/35 pb-8 lg:grid-cols-12 lg:items-end lg:gap-12" data-reveal>
                    <p class="max-w-[10rem] font-display text-base font-semibold leading-5 text-white lg:col-span-4">Catalyst<br>Mentorship Track</p>
                    <div class="lg:col-span-8 lg:justify-self-end">
                        <h1 id="pre-event-one-hero-title" class="max-w-4xl font-display text-4xl font-semibold leading-[1.02] tracking-[-0.035em] sm:text-5xl lg:text-6xl">Build the idea before you bring it to the Summit.</h1>
                        <p class="mt-6 max-w-4xl text-base leading-7 text-white/85 sm:text-lg sm:leading-8">An intensive mentoring program for students developing business cases and business plans in renewable energy, guided by mentors before the final pitching stage.</p>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-between gap-5 text-xs font-medium tracking-[0.04em] text-white/70">
                    <img class="size-6" src="{{ asset('images/icon/arrow-down-icon-mainevent.svg') }}" width="24" height="23" alt="">
                    <span>Scroll to explore</span>
                </div>
            </x-ui.container>
        </section>

        <section id="about" class="scroll-mt-32 bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="about-pre-event-one-title">
            <x-ui.container>
                <header class="mx-auto max-w-4xl text-center" data-reveal>
                    <x-public.section-label class="justify-center">About Pre-Event 1</x-public.section-label>
                    <h2 id="about-pre-event-one-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">The first phase of<br class="hidden sm:block"> Catalyst 2026</h2>
                    <div class="mx-auto mt-10 max-w-4xl space-y-6 text-base leading-7 text-catalyst-muted sm:text-lg sm:leading-8">
                        <p>Catalyst Mentorship Track is an intensive program designed to help participants develop stronger business plans and business cases in the renewable-energy sector through structured learning and direct mentorship.</p>
                        <p>Participants move from foundational learning and track-specific classes to mentoring, professional feedback, and final pitching before taking their ideas toward the wider Catalyst Summit journey.</p>
                    </div>
                </header>
            </x-ui.container>
        </section>

        <section class="bg-white pb-16 sm:pb-24 lg:pb-28" data-navbar-theme="dark" aria-labelledby="helios-title">
            <x-ui.container>
                <div class="grid gap-8 lg:grid-cols-12 lg:items-end" data-reveal>
                    <div class="lg:col-span-5">
                        <x-public.section-label>Event theme</x-public.section-label>
                        <h2 id="helios-title" class="mt-5 font-display text-5xl font-semibold leading-none tracking-tight text-catalyst-ink sm:text-6xl">Helios</h2>
                    </div>
                    <p class="w-full max-w-[27rem] text-left font-display text-xl font-semibold leading-8 text-catalyst-ink lg:col-span-5 lg:col-start-8 lg:ml-auto lg:justify-self-end">
                        <span class="lg:whitespace-nowrap">Harnessing Equitable Leadership through</span><br class="hidden lg:block">
                        <span class="lg:whitespace-nowrap">Innovation, Opportunity, and Synergy</span>
                    </p>
                </div>
            </x-ui.container>

            <figure class="mt-12" data-reveal>
                {{-- TODO: Replace this branded fallback with the approved Helios programme photography. --}}
                <div class="relative grid min-h-72 place-items-center overflow-hidden bg-[radial-gradient(circle_at_72%_36%,rgba(198,211,79,.72),transparent_22%),linear-gradient(115deg,#006d6a_0%,#6ab266_52%,#f7f9f7_100%)] px-6 text-center sm:min-h-96 lg:min-h-[34rem]" data-content-todo="helios-programme-photography">
                    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-catalyst-ink/35 to-transparent" aria-hidden="true"></div>
                    <div class="relative text-white">
                        <img class="mx-auto h-20 w-auto brightness-0 invert" src="{{ asset('images/brand/catalyst-mark.png') }}" width="44" height="80" loading="lazy" decoding="async" alt="">
                        <p class="mt-5 font-display text-sm font-semibold tracking-[0.12em]">HELIOS · CATALYST MENTORSHIP TRACK</p>
                        <p class="mt-2 text-xs text-white/80">Official programme photography coming soon</p>
                    </div>
                </div>
                <x-ui.container><figcaption class="mt-5 max-w-3xl text-sm leading-6 text-catalyst-muted sm:text-base sm:leading-7">Inspired by the sun as an energy source without boundaries, HELIOS reflects a clean-energy transition that is equitable, inclusive, and grounded in shared leadership.</figcaption></x-ui.container>
            </figure>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="experience-title">
            <x-ui.container>
                <header class="max-w-3xl" data-reveal>
                    <x-public.section-label>Event experience</x-public.section-label>
                    <h2 id="experience-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">From foundation to final pitch.</h2>
                </header>

                <ol class="mt-12 border-t border-catalyst-grey/30 lg:mt-16" data-reveal>
                    @foreach ($experiences as $experience)
                        <li class="grid gap-5 border-b border-catalyst-grey/30 py-7 md:grid-cols-[8rem_minmax(13rem,1fr)_minmax(18rem,1.35fr)] md:items-start md:gap-10 lg:py-9">
                            <span @class(['font-display text-4xl font-semibold leading-none sm:text-5xl', 'text-catalyst-primary' => $loop->first, 'text-catalyst-grey/45' => ! $loop->first])>{{ $experience['number'] }}</span>
                            <h3 @class(['font-display text-xl font-semibold leading-8 sm:text-2xl', 'text-catalyst-primary' => $loop->first, 'text-catalyst-grey/60' => ! $loop->first])>{{ $experience['title'] }}</h3>
                            <p @class(['max-w-xl text-sm leading-6 sm:text-base', 'text-catalyst-primary' => $loop->first, 'text-catalyst-grey/60' => ! $loop->first])>{{ $experience['copy'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="pre-event-timeline-title">
            <x-ui.container>
                <header class="max-w-3xl" data-reveal>
                    <x-public.section-label>Pre-Event 1 timeline</x-public.section-label>
                    <h2 id="pre-event-timeline-title" class="mt-5 font-display text-4xl font-semibold tracking-tight text-catalyst-ink sm:text-5xl">How the event unfolded.</h2>
                </header>

                <ol class="relative mt-14 border-l border-dashed border-catalyst-primary/35 pl-9 lg:hidden" data-reveal>
                    @foreach ($timeline as [$date, $label, $start, $end])
                        @php($state = $timelineState($start, $end))
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
                            <time class="font-display text-xl font-semibold text-catalyst-ink sm:text-2xl">{{ $date }}</time>
                            <p class="mt-2 max-w-xs text-sm leading-5 text-catalyst-muted sm:text-base">{{ $label }}</p>
                        </li>
                    @endforeach
                </ol>

                <div class="relative mt-20 hidden lg:block" data-reveal>
                    <span class="absolute left-4 right-4 top-4 border-t border-dashed border-catalyst-primary/40" aria-hidden="true"></span>
                    <ol class="relative grid grid-cols-5 gap-6" aria-label="Catalyst Mentorship Track milestones">
                        @foreach ($timeline as [$date, $label, $start, $end])
                            @php($state = $timelineState($start, $end))
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
                                <time class="font-display text-xl font-semibold text-catalyst-ink sm:text-2xl">{{ $date }}</time>
                                <p class="mt-3 max-w-[11rem] text-sm leading-5 text-catalyst-muted sm:text-base">{{ $label }}</p>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="people-title">
            <x-ui.container>
                <header class="max-w-4xl" data-reveal>
                    <x-public.section-label>People of the program</x-public.section-label>
                    <h2 id="people-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">Mentors and judges behind the track.</h2>
                </header>

                <div class="mt-12 grid gap-x-5 gap-y-10 sm:grid-cols-2 lg:mt-16 lg:grid-cols-4" data-reveal>
                    @foreach ($people as [$role, $focus])
                        <article data-program-person>
                            <div class="relative grid aspect-[3/4] place-items-center overflow-hidden border border-catalyst-primary/20 bg-catalyst-surface p-5 text-center">
                                <span class="absolute left-4 top-4 bg-white px-3 py-1.5 text-xs font-semibold text-catalyst-primary">{{ $role }}</span>
                                <div>
                                    <img class="mx-auto h-16 w-auto opacity-70" src="{{ asset('images/brand/catalyst-mark.png') }}" width="35" height="64" loading="lazy" decoding="async" alt="">
                                    <p class="mt-4 text-xs font-medium leading-5 text-catalyst-primary">Official announcement<br>coming soon</p>
                                </div>
                            </div>
                            <h3 class="mt-4 font-display text-base font-semibold text-catalyst-ink">To Be Announced</h3>
                            <p class="mt-1 text-xs leading-5 text-catalyst-muted">{{ $focus }}</p>
                        </article>
                    @endforeach
                </div>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="highlights-title">
            <x-ui.container>
                <header class="mx-auto max-w-3xl text-center" data-reveal>
                    <x-public.section-label class="justify-center">Event highlights</x-public.section-label>
                    <h2 id="highlights-title" class="mt-5 font-display text-4xl font-semibold tracking-tight text-catalyst-ink sm:text-5xl">Inside the Mentorship Track.</h2>
                </header>
            </x-ui.container>

            {{-- TODO: Replace the temporary project photography with approved Mentorship Track documentation. --}}
            <div class="home-people-marquee mt-12 lg:mt-16" data-reveal data-content-todo="mentorship-documentation" aria-label="Mentorship Track event highlights">
                <div class="home-people-marquee__track">
                    @foreach ([false, true] as $duplicate)
                        <div class="home-people-marquee__group" @if ($duplicate) aria-hidden="true" @endif>
                            @foreach ($highlightImages as [$image, $position])
                                <figure class="aspect-[925/600] w-[78vw] min-w-[18rem] max-w-[57.8125rem] shrink-0 overflow-hidden">
                                    <img class="size-full {{ $position }} object-cover" src="{{ asset($image) }}" width="925" height="600" loading="lazy" decoding="async" alt="">
                                </figure>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

            <x-ui.container>
                <p class="mx-auto mt-10 max-w-5xl text-center font-display text-2xl font-normal leading-7 text-neutral-900 sm:mt-12" data-reveal>
                    A structured mix of classes, mentoring, collaborative discussion, and final pitching<br class="hidden lg:block">
                    designed to move each team from early ideas to a sharper solution.
                </p>
            </x-ui.container>
        </section>

        <section id="registration" class="scroll-mt-32 bg-white py-10 sm:py-16 lg:pb-24" data-navbar-theme="dark" aria-labelledby="registration-title next-step-title">
            <x-ui.container>
                <div class="main-event-final-card relative isolate grid gap-20 overflow-hidden border border-catalyst-primary/20 p-7 sm:p-10 lg:min-h-[58rem] lg:grid-rows-2 lg:gap-12 lg:p-16" data-reveal>
                    <div class="grid lg:grid-cols-12 lg:items-start">
                        <div class="w-full max-w-xl text-left lg:col-span-5 lg:col-start-8 lg:justify-self-end">
                            <x-public.section-label>Registration</x-public.section-label>
                            <h2 id="registration-title" class="mt-5 font-display text-3xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-4xl">Ready to join the Mentorship Track?</h2>
                            <p class="mt-4 max-w-2xl text-sm leading-6 text-catalyst-muted sm:text-base sm:leading-7">Open to active D3, D4, and S1 students in teams of 2–3 members. Registration runs from 20–27 September 2026.</p>
                            <div class="mt-8 flex flex-col items-start gap-3 sm:flex-row">
                                <a class="group inline-flex min-h-12 w-full items-center justify-center gap-2 bg-catalyst-primary px-5 py-3 text-sm font-semibold text-white hover:bg-catalyst-green sm:w-auto" href="{{ route('register') }}">Register Now<x-public.link-arrow class="brightness-0 invert transition-transform duration-200 group-hover:translate-x-1" /></a>
                                @if ($guidebookUrl)
                                    <a class="inline-flex min-h-12 w-full items-center justify-center gap-2 border border-catalyst-primary px-5 py-3 text-sm font-semibold text-catalyst-primary hover:bg-white/70 sm:w-auto" href="{{ $guidebookUrl }}" target="_blank" rel="noopener noreferrer">View Guidebook<x-public.link-arrow external /></a>
                                @else
                                    <span class="inline-flex min-h-12 w-full cursor-not-allowed items-center justify-center gap-2 border border-catalyst-primary px-5 py-3 text-sm font-semibold text-catalyst-primary opacity-60 sm:w-auto" role="link" aria-disabled="true" data-link-todo="mentorship-guidebook" title="Mentorship Track guidebook URL to be confirmed">View Guidebook<x-public.link-arrow external /></span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="grid lg:grid-cols-12 lg:items-end">
                        <div class="max-w-3xl lg:col-span-6 lg:self-end">
                            <x-public.section-label>Next in Catalyst</x-public.section-label>
                            <h2 id="next-step-title" class="mt-5 font-display text-3xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-4xl">Take the next step toward Catalyst Summit.</h2>
                            <p class="mt-5 max-w-2xl text-base leading-7 text-catalyst-muted">Bring the ideas refined through mentorship into the wider Catalyst journey and explore the competition, exhibition, and Summit experiences.</p>
                            <div class="mt-8 flex flex-col items-start gap-3 sm:flex-row">
                                <a class="group inline-flex min-h-12 w-full items-center justify-center gap-2 bg-catalyst-primary px-5 py-3 text-sm font-semibold text-white hover:bg-catalyst-green sm:w-auto" href="{{ route('main-event.index') }}">Explore Catalyst Summit<x-public.link-arrow class="brightness-0 invert transition-transform duration-200 group-hover:translate-x-1" /></a>
                                <a class="group inline-flex min-h-12 w-full items-center justify-center gap-2 border border-catalyst-primary px-5 py-3 text-sm font-semibold text-catalyst-primary hover:bg-white/70 sm:w-auto" href="{{ route('pre-event-2.index') }}">View Catalyst Green Action<x-public.link-arrow class="transition-transform duration-200 group-hover:translate-x-1" /></a>
                            </div>
                        </div>
                    </div>
                </div>
            </x-ui.container>
        </section>
    </div>
@endsection
