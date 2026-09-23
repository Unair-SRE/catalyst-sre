@php
    $navigation = [
        ['label' => 'Competitions', 'route' => 'competitions.index', 'active' => 'competitions.*'],
        ['label' => 'Pre-Event 1', 'route' => 'pre-event-1.index', 'active' => 'pre-event-1.*'],
        ['label' => 'Pre-Event 2', 'route' => 'pre-event-2.index', 'active' => 'pre-event-2.*'],
        ['label' => 'Main Event', 'route' => 'main-event.index', 'active' => 'main-event.*'],
    ];

    $guidebookUrl = config('services.catalyst.guidebook_url');
    $registerUrl = Route::has('register') ? route('register') : null;
@endphp

<header
    class="public-navbar"
    data-public-navbar
    data-active-route="{{ request()->route()?->getName() }}"
    data-scrolled="false"
    data-foreground="dark"
    data-menu-open="false"
>
    <div class="public-navbar__surface">
        <a class="public-navbar__brand shrink-0" href="{{ route('home') }}" aria-label="Catalyst Summit home">
            <x-public.logo adaptive />
        </a>

        <nav class="public-navbar__desktop-nav hidden items-center gap-4 lg:flex" aria-label="Primary navigation" data-primary-navigation>
            @foreach ($navigation as $item)
                @php($isActive = request()->routeIs($item['active']))
                <a
                    class="public-navbar__nav-link px-3 py-3 text-sm font-medium"
                    href="{{ route($item['route']) }}"
                    data-nav-route="{{ $item['route'] }}"
                    data-active="{{ $isActive ? 'true' : 'false' }}"
                    @if ($isActive) aria-current="page" @endif
                >{{ $item['label'] }}</a>
            @endforeach
        </nav>

        <div class="hidden shrink-0 items-center gap-3 lg:flex">
            @if ($guidebookUrl)
                <a class="public-navbar__secondary inline-flex min-h-12 items-center justify-center border px-4 py-3.5 text-sm font-medium" href="{{ $guidebookUrl }}" target="_blank" rel="noopener noreferrer">Guidebook</a>
            @else
                <span class="public-navbar__secondary inline-flex min-h-12 items-center justify-center border px-4 py-3.5 text-sm font-medium opacity-60" role="link" aria-disabled="true" data-link-todo="guidebook" title="Guidebook URL to be confirmed">Guidebook</span>
            @endif

            @if ($registerUrl)
                <a class="public-navbar__primary inline-flex min-h-12 items-center justify-center bg-catalyst-primary px-4 py-3.5 text-sm font-medium text-white" href="{{ $registerUrl }}">Register Now</a>
            @else
                <span class="public-navbar__primary inline-flex min-h-12 items-center justify-center bg-catalyst-primary px-4 py-3.5 text-sm font-medium text-white opacity-60" role="link" aria-disabled="true" data-link-todo="register">Register Now</span>
            @endif
        </div>

        <details class="group relative lg:hidden" data-public-navbar-menu>
            <summary class="public-navbar__toggle flex size-11 list-none items-center justify-center [&::-webkit-details-marker]:hidden" data-public-navbar-toggle aria-label="Open navigation" aria-expanded="false">
                <svg class="size-6 group-open:hidden" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <path d="M4 7h16M4 12h16M4 17h16" />
                </svg>
                <svg class="hidden size-6 group-open:block" aria-hidden="true" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <path d="m6 6 12 12M18 6 6 18" />
                </svg>
            </summary>

            <div class="public-navbar__mobile-panel absolute right-0 top-14 w-[calc(100vw-2.5rem)] max-w-sm border border-catalyst-ink/10 bg-white p-3 text-catalyst-ink shadow-lg shadow-catalyst-ink/5">
                <nav class="flex flex-col" aria-label="Mobile navigation">
                    @foreach ($navigation as $item)
                        @php($isActive = request()->routeIs($item['active']))
                        <a
                            class="px-4 py-3 text-sm font-medium {{ $isActive ? 'text-catalyst-primary' : 'text-catalyst-muted hover:bg-catalyst-neutral hover:text-catalyst-ink' }}"
                            href="{{ route($item['route']) }}"
                            data-nav-route="{{ $item['route'] }}"
                            data-active="{{ $isActive ? 'true' : 'false' }}"
                            @if ($isActive) aria-current="page" @endif
                        >{{ $item['label'] }}</a>
                    @endforeach
                </nav>

                <div class="mt-3 grid gap-2 border-t border-catalyst-ink/10 pt-3 sm:grid-cols-2">
                    @if ($guidebookUrl)
                        <a class="inline-flex min-h-11 items-center justify-center border border-catalyst-primary px-4 py-2.5 text-sm font-medium text-catalyst-primary" href="{{ $guidebookUrl }}" target="_blank" rel="noopener noreferrer">Guidebook</a>
                    @else
                        <span class="inline-flex min-h-11 items-center justify-center border border-catalyst-primary px-4 py-2.5 text-sm font-medium text-catalyst-primary opacity-60" role="link" aria-disabled="true" data-link-todo="guidebook">Guidebook</span>
                    @endif

                    @if ($registerUrl)
                        <a class="inline-flex min-h-11 items-center justify-center bg-catalyst-primary px-4 py-2.5 text-sm font-medium text-white" href="{{ $registerUrl }}">Register Now</a>
                    @else
                        <span class="inline-flex min-h-11 items-center justify-center bg-catalyst-primary px-4 py-2.5 text-sm font-medium text-white opacity-60" role="link" aria-disabled="true" data-link-todo="register">Register Now</span>
                    @endif
                </div>
            </div>
        </details>
    </div>
</header>

<div class="public-navbar-spacer" aria-hidden="true"></div>
