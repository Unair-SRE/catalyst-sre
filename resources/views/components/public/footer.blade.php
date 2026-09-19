@php
    $guidebookUrl = config('services.catalyst.guidebook_url');
    $groups = [
        'Explore' => [
            ['label' => 'Home', 'url' => route('home')],
            ['label' => 'Pre-Event 1', 'url' => route('pre-event-1.index')],
            ['label' => 'Pre-Event 2', 'url' => route('pre-event-2.index')],
            ['label' => 'Catalyst Summit', 'url' => route('main-event.index')],
        ],
        'Competition' => [
            ['label' => 'MCC', 'url' => null],
            ['label' => 'BCC', 'url' => null],
            ['label' => 'BPC', 'url' => null],
        ],
        'Resource' => [
            ['label' => 'Guidebooks', 'url' => $guidebookUrl, 'external' => true],
            ['label' => 'FAQ', 'url' => null],
            ['label' => 'Timeline', 'url' => null],
        ],
        'Connect' => [
            ['label' => 'Instagram', 'url' => null],
            ['label' => 'Contact', 'url' => null],
            ['label' => 'SRE UNAIR', 'url' => null],
        ],
    ];
@endphp

<footer data-public-footer class="public-footer relative min-h-[700px] overflow-hidden bg-white">
    <div class="pointer-events-none absolute inset-x-0 bottom-0 h-64" data-navbar-theme="light" aria-hidden="true"></div>
    <img
        class="public-footer__image"
        src="{{ asset('images/brand/footer-image.webp') }}"
        width="1440"
        height="700"
        loading="lazy"
        decoding="async"
        alt=""
        aria-hidden="true"
    >

    <x-ui.container class="relative z-10 pb-64 pt-14 sm:pb-72 sm:pt-20 lg:pt-24">
        <div class="grid gap-12 xl:grid-cols-12 xl:gap-16">
            <div class="max-w-[469px] xl:col-span-5">
                <a class="inline-flex" href="{{ route('home') }}" aria-label="Catalyst Summit home">
                    <x-public.logo :stacked="false" />
                </a>
                <p class="mt-5 max-w-md text-sm leading-6 text-catalyst-ink/75 sm:text-base">
                    Catalyst 2026 connects pre-events, competitions, and Catalyst Summit in one participant journey.
                </p>
            </div>

            <div class="grid grid-cols-2 gap-x-8 gap-y-10 sm:grid-cols-4 xl:col-span-7 xl:justify-self-end xl:gap-x-14">
                @foreach ($groups as $heading => $links)
                    <nav aria-label="Footer {{ strtolower($heading) }}">
                        <h2 class="text-xs font-semibold uppercase tracking-[0.14em] text-catalyst-ink">{{ $heading }}</h2>
                        <ul class="mt-4 space-y-3 text-sm text-catalyst-ink/75">
                            @foreach ($links as $link)
                                <li>
                                    @if ($link['url'])
                                        <a class="hover:text-catalyst-primary" href="{{ $link['url'] }}" @if ($link['external'] ?? false) target="_blank" rel="noopener noreferrer" @endif>{{ $link['label'] }}</a>
                                    @else
                                        <span class="cursor-not-allowed opacity-55" aria-disabled="true" data-link-todo="{{ strtolower(str_replace(' ', '-', $link['label'])) }}" title="Destination to be confirmed">{{ $link['label'] }}</span>
                                    @endif
                                </li>
                            @endforeach
                        </ul>
                    </nav>
                @endforeach
            </div>
        </div>

        <div class="mt-12 flex flex-col gap-4 border-t border-catalyst-ink/15 pt-5 text-xs text-catalyst-ink/70 sm:mt-16 sm:flex-row sm:items-center sm:justify-between">
            <p>&copy; {{ now()->year }} Catalyst Summit. All rights reserved.</p>
            <div class="flex flex-wrap items-center gap-3" aria-label="Legal links">
                <span class="cursor-not-allowed opacity-55" aria-disabled="true" data-link-todo="privacy-policy">Kebijakan Privasi</span>
                <span aria-hidden="true">/</span>
                <span class="cursor-not-allowed opacity-55" aria-disabled="true" data-link-todo="terms-and-conditions">Syarat dan Ketentuan</span>
            </div>
        </div>
    </x-ui.container>
</footer>
