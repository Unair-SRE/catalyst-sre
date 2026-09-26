@extends('layouts.public')

@section('title', 'Catalyst Green Action — Catalyst 2026')

@section('content')
    @php
        $experiences = [
            ['number' => '01', 'title' => 'Mangrove Planting', 'copy' => 'Participants took part in an eco-restoration activity at Ekowisata Mangrove Wonorejo, contributing directly to a greener coastal environment.'],
            ['number' => '02', 'title' => 'Renewable Energy Education', 'copy' => 'The program paired environmental action with learning around renewable energy and sustainability.'],
            ['number' => '03', 'title' => 'Community Connection', 'copy' => 'Participants shared the day with others who care about meaningful environmental action and a more sustainable future.'],
        ];

        $eventFacts = [
            ['value' => 'Surabaya', 'label' => 'Place', 'detail' => 'Ekowisata Mangrove Wonorejo'],
            ['value' => 'IDR 38K', 'label' => 'Fee', 'detail' => 'Participation Fee'],
            ['value' => '13 September 2026', 'label' => 'Event Date', 'detail' => null],
        ];

        $participantBenefits = [
            ['01', 'Real Environmental Action', 'Take part in an eco-restoration movement and contribute directly to a greener future.', 'images/icon/realworld-whycatalyst-mainevent-icon.svg'],
            ['02', 'New Connections', 'Meet people who share the same interest in creating meaningful environmental change.', 'images/icon/connection-whycatalyst-mainevent-icon.svg'],
            ['03', 'Hands-On Experience', 'Step outside the classroom and learn through real environmental action.', 'images/icon/mentorship-icon-whyparticipate.svg'],
            ['04', 'Certificate + SKP', 'Participants receive a certificate, with SKP available for Universitas Airlangga students.', 'images/icon/recognition-icon-whyparticipate.svg'],
        ];

        $highlightImages = [
            ['images/brand/footer-image.webp', 'object-left'],
            ['images/brand/footer-image.webp', 'object-center'],
            ['images/brand/footer-image.webp', 'object-right'],
        ];
    @endphp

    <div class="home-page overflow-clip" data-pre-event-two-page>
        <section
            class="relative -mt-24 flex min-h-[42rem] items-end overflow-hidden bg-catalyst-ink pb-8 pt-36 text-white sm:-mt-28 sm:min-h-[46rem] sm:pb-12 lg:-mt-36 lg:min-h-[52rem] lg:pb-14"
            data-navbar-theme="light"
            aria-labelledby="pre-event-two-hero-title"
        >
            <img class="absolute inset-0 size-full scale-105 object-cover object-center" src="{{ asset('images/brand/footer-image.webp') }}" width="1440" height="700" fetchpriority="high" decoding="async" alt="">
            <div class="absolute inset-0 bg-[linear-gradient(180deg,rgba(23,33,38,.18)_0%,rgba(23,33,38,.38)_42%,rgba(10,26,27,.92)_100%)]" aria-hidden="true"></div>

            <x-ui.container class="relative z-10 w-full">
                <div class="grid gap-8 border-b border-white/35 pb-8 lg:grid-cols-12 lg:items-end lg:gap-12" data-reveal>
                    <p class="max-w-[10rem] font-display text-base font-semibold leading-5 text-white lg:col-span-4">Catalyst<br>Green<br>Action</p>
                    <div class="lg:col-span-8 lg:justify-self-end">
                        <h1 id="pre-event-two-hero-title" class="max-w-4xl font-display text-4xl font-semibold leading-[1.02] tracking-[-0.035em] sm:text-5xl lg:text-6xl">Turn environmental concern into real action.</h1>
                        <p class="mt-6 max-w-4xl text-base leading-7 text-white/85 sm:text-lg sm:leading-8">Catalyst Green Action brought participants together for mangrove planting and renewable energy education through a hands-on environmental experience in Surabaya.</p>
                    </div>
                </div>

                <div class="mt-5 flex items-center justify-between gap-5 text-xs font-medium tracking-[0.04em] text-white/70">
                    <img class="size-6" src="{{ asset('images/icon/arrow-down-icon-mainevent.svg') }}" width="24" height="23" alt="">
                    <span>Scroll to explore</span>
                </div>
            </x-ui.container>
        </section>

        <section id="about" class="scroll-mt-32 bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="about-pre-event-two-title">
            <x-ui.container>
                <header class="mx-auto max-w-4xl text-center" data-reveal>
                    <x-public.section-label class="justify-center">About Pre-Event 2</x-public.section-label>
                    <h2 id="about-pre-event-two-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">From awareness<br class="hidden sm:block"> to environmental action.</h2>
                    <div class="mx-auto mt-10 max-w-4xl space-y-6 text-base leading-7 text-catalyst-muted sm:text-lg sm:leading-8">
                        <p>Catalyst Green Action is an environmental initiative under Catalyst Summit that invited participants to take direct action through mangrove planting and renewable energy education.</p>
                        <p>Held at Ekowisata Mangrove Wonorejo, the program combined eco-restoration with learning and community interaction, turning sustainability from a conversation into a shared experience.</p>
                    </div>
                </header>
            </x-ui.container>
        </section>

        <section class="bg-white pb-16 sm:pb-24 lg:pb-28" data-navbar-theme="dark" aria-labelledby="synergy-title">
            <x-ui.container>
                <div class="grid gap-8 lg:grid-cols-12 lg:items-end" data-reveal>
                    <div class="lg:col-span-5">
                        <x-public.section-label>Event theme</x-public.section-label>
                        <h2 id="synergy-title" class="mt-5 font-display text-5xl font-semibold leading-none tracking-tight text-catalyst-ink sm:text-6xl">Synergy</h2>
                    </div>
                    <p class="w-full max-w-[27rem] text-left font-display text-xl font-semibold leading-8 text-catalyst-ink lg:col-span-5 lg:col-start-8 lg:ml-auto lg:justify-self-end">
                        <span class="lg:whitespace-nowrap">Synergizing Eco-Restoration</span><br class="hidden lg:block">
                        <span class="lg:whitespace-nowrap">&amp; Green Youth</span>
                    </p>
                </div>
            </x-ui.container>

            <figure class="mt-12" data-reveal>
                {{-- TODO: Replace this branded fallback with approved Green Action programme photography. --}}
                <div class="relative grid min-h-72 place-items-center overflow-hidden bg-[radial-gradient(circle_at_72%_36%,rgba(198,211,79,.72),transparent_22%),linear-gradient(115deg,#006d6a_0%,#6ab266_52%,#f7f9f7_100%)] px-6 text-center sm:min-h-96 lg:min-h-[34rem]" data-content-todo="green-action-programme-photography">
                    <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-catalyst-ink/35 to-transparent" aria-hidden="true"></div>
                    <div class="relative text-white">
                        <img class="mx-auto h-20 w-auto brightness-0 invert" src="{{ asset('images/brand/catalyst-mark.png') }}" width="44" height="80" loading="lazy" decoding="async" alt="">
                        <p class="mt-5 font-display text-sm font-semibold tracking-[0.12em]">SYNERGY · CATALYST GREEN ACTION</p>
                        <p class="mt-2 text-xs text-white/80">Official programme photography coming soon</p>
                    </div>
                </div>
                <x-ui.container><figcaption class="mt-5 max-w-3xl text-sm leading-6 text-catalyst-muted sm:text-base sm:leading-7">SYNERGY brings young people and environmental action together through restoration, shared learning, and practical participation in a greener future.</figcaption></x-ui.container>
            </figure>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="experience-title">
            <x-ui.container>
                <header class="max-w-3xl" data-reveal>
                    <x-public.section-label>Event experience</x-public.section-label>
                    <h2 id="experience-title" class="mt-5 font-display text-4xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-5xl">A day built around action.</h2>
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

        <section class="bg-white py-16 sm:py-20 lg:py-24" data-navbar-theme="dark" aria-labelledby="green-action-glance-title">
            <x-ui.container>
                <div class="grid gap-6 lg:grid-cols-12 lg:items-end" data-reveal>
                    <div class="lg:col-span-7">
                        <x-public.section-label>Pre-Event 2 details</x-public.section-label>
                        <h2 id="green-action-glance-title" class="mt-5 font-display text-4xl font-semibold tracking-tight text-catalyst-ink sm:text-5xl">Green Action at a glance.</h2>
                    </div>
                </div>

                <dl class="mt-12 grid sm:mt-16 sm:grid-cols-3" data-reveal>
                    @foreach ($eventFacts as $fact)
                        <div class="border-b border-catalyst-grey/30 px-3 py-7 text-center last:border-b-0 sm:border-b-0 sm:border-r sm:px-8 lg:px-10 sm:last:border-r-0" data-green-action-fact>
                            <dd class="font-display text-3xl font-semibold tracking-tight text-catalyst-green sm:text-4xl">{{ $fact['value'] }}</dd>
                            <dt class="mt-3 text-sm font-medium text-catalyst-ink sm:text-base">{{ $fact['label'] }}</dt>
                            @if ($fact['detail'])
                                <p class="mt-2 text-xs leading-5 text-catalyst-muted sm:text-sm">{{ $fact['detail'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </dl>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="participant-benefits-title">
            <x-ui.container>
                <header class="max-w-4xl" data-reveal>
                    <x-public.section-label>What Participants Got</x-public.section-label>
                    <h2 id="participant-benefits-title" class="mt-5 font-display text-4xl font-semibold tracking-tight text-catalyst-ink sm:text-5xl">Impact beyond the activity itself.</h2>
                </header>

                <ol class="mt-12 border-t border-neutral-200 lg:mt-16" data-reveal>
                    @foreach ($participantBenefits as [$number, $title, $copy, $icon])
                        <li class="grid grid-cols-[3.5rem_minmax(0,1fr)] gap-x-5 gap-y-4 border-b border-neutral-200 py-7 lg:grid-cols-[3.5rem_minmax(15rem,1fr)_minmax(20rem,30rem)] lg:items-center lg:gap-x-16 xl:gap-x-28" data-participant-benefit>
                            <span class="font-display text-5xl font-semibold leading-10 text-catalyst-primary">{{ $number }}</span>
                            <div class="flex items-center gap-5">
                                <img class="size-6 shrink-0" src="{{ asset($icon) }}" width="24" height="24" loading="lazy" decoding="async" alt="">
                                <h3 class="font-display text-xl font-semibold leading-8 text-catalyst-ink">{{ $title }}</h3>
                            </div>
                            <p class="col-start-2 text-base leading-6 text-catalyst-muted lg:col-start-auto lg:text-right">{{ $copy }}</p>
                        </li>
                    @endforeach
                </ol>
            </x-ui.container>
        </section>

        <section class="bg-white py-16 sm:py-24 lg:py-28" data-navbar-theme="dark" aria-labelledby="highlights-title">
            <x-ui.container>
                <header class="mx-auto max-w-3xl text-center" data-reveal>
                    <x-public.section-label class="justify-center">Event highlights</x-public.section-label>
                    <h2 id="highlights-title" class="mt-5 font-display text-4xl font-semibold tracking-tight text-catalyst-ink sm:text-5xl">Green Action, in pictures.</h2>
                </header>
            </x-ui.container>

            {{-- TODO: Replace the temporary project photography with approved Green Action documentation. --}}
            <div class="home-people-marquee mt-12 lg:mt-16" data-reveal data-content-todo="green-action-documentation" aria-label="Green Action event highlights">
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
                    A selection of moments<br>
                    from the first phase of Catalyst 2026.
                </p>
            </x-ui.container>
        </section>

        <section id="collaboration" class="scroll-mt-32 bg-white py-10 sm:py-16 lg:pb-24" data-navbar-theme="dark" aria-labelledby="collaboration-title next-step-title">
            <x-ui.container>
                <div class="main-event-final-card relative isolate grid gap-20 overflow-hidden border border-catalyst-primary/20 p-7 sm:p-10 lg:min-h-[58rem] lg:grid-rows-2 lg:gap-12 lg:p-16" data-reveal>
                    <div class="grid lg:grid-cols-12 lg:items-start">
                        <div class="w-full max-w-xl text-left lg:col-span-5 lg:col-start-8 lg:justify-self-end">
                            <x-public.section-label>In collaboration with</x-public.section-label>
                            <h2 id="collaboration-title" class="mt-5 font-display text-3xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-4xl">Green Action × HEROGREEN 2026</h2>
                            <p class="mt-4 max-w-2xl text-sm leading-6 text-catalyst-muted sm:text-base sm:leading-7">A collaborative environmental action under the Catalyst Summit journey.</p>
                        </div>
                    </div>

                    <div class="grid lg:grid-cols-12 lg:items-end">
                        <div class="max-w-3xl lg:col-span-6 lg:self-end">
                            <x-public.section-label>Next in Catalyst</x-public.section-label>
                            <h2 id="next-step-title" class="mt-5 font-display text-3xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-4xl">Continue to Main Event</h2>
                            <p class="mt-5 max-w-2xl text-base leading-7 text-catalyst-muted">Carry the spirit of action forward into the competitions, conversations, and final showcase of Catalyst Summit 2026.</p>
                            <div class="mt-8 flex flex-col items-start gap-3 sm:flex-row">
                                <a class="group inline-flex min-h-12 w-full items-center justify-center gap-2 bg-catalyst-primary px-5 py-3 text-sm font-semibold text-white hover:bg-catalyst-green sm:w-auto" href="{{ route('main-event.index') }}">Explore Main Event<x-public.link-arrow class="brightness-0 invert transition-transform duration-200 group-hover:translate-x-1" /></a>
                                <a class="group inline-flex min-h-12 w-full items-center justify-center gap-2 border border-catalyst-primary px-5 py-3 text-sm font-semibold text-catalyst-primary hover:bg-white/70 sm:w-auto" href="{{ route('pre-event-1.index') }}">View Pre-Event 1<x-public.link-arrow class="transition-transform duration-200 group-hover:translate-x-1" /></a>
                            </div>
                        </div>
                    </div>
                </div>
            </x-ui.container>
        </section>
    </div>
@endsection
