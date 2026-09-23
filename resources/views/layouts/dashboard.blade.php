<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@hasSection('title')@yield('title') | @endif{{ config('app.name') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="dashboard bg-catalyst-background font-sans text-catalyst-ink antialiased">
        <a href="#dashboard-content" class="sr-only fixed top-3 left-3 z-50 bg-white p-3 focus:not-sr-only">Skip to content</a>
        @php($dashboardNavigation = match (true) {
            request()->routeIs('dashboard.team.*') => 'team',
            request()->routeIs('dashboard.registration.*') => 'registration',
            request()->routeIs('dashboard.submission.*') => 'submission',
            request()->routeIs('dashboard.summit-pass.*') => 'summit-pass',
            request()->routeIs('dashboard.profile.*') => 'profile',
            default => 'overview',
        })
        <div class="min-h-screen lg:grid lg:grid-cols-[16rem_minmax(0,1fr)]">
            <aside class="sticky top-0 hidden h-dvh border-catalyst-grey/30 border-r bg-catalyst-neutral lg:block" aria-label="Dashboard navigation">
                <x-dashboard.navigation :active="$dashboardNavigation" />
            </aside>

            <div class="min-w-0">
                <header class="border-catalyst-grey/30 border-b bg-white lg:hidden">
                    <div class="flex min-h-16 items-center justify-between px-5 sm:px-6">
                        <a class="inline-flex items-center gap-3 font-display text-sm font-semibold tracking-wide focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-catalyst-primary" href="{{ route('dashboard.index') }}">
                            <span class="grid size-9 place-items-center rounded-full bg-catalyst-primary font-sans text-sm font-bold text-white" aria-hidden="true">C</span>
                            <span>Catalyst 2026</span>
                        </a>

                        <details class="group relative" x-data @keydown.escape="$el.open = false; $refs.navToggle.focus()" @click.outside="$el.open = false">
                            <summary x-ref="navToggle" aria-label="Open navigation" class="flex size-11 cursor-pointer list-none items-center justify-center text-catalyst-ink [&::-webkit-details-marker]:hidden focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary">
                                <svg class="size-5 group-open:hidden" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16" stroke="currentColor" stroke-linecap="round" stroke-width="1.5" /></svg>
                                <svg class="hidden size-5 group-open:block" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m6 6 12 12M6 18 18 6" stroke="currentColor" stroke-linecap="round" stroke-width="1.5" /></svg>
                            </summary>
                            <div class="absolute right-0 top-full z-20 mt-2 max-h-[80dvh] w-72 max-w-[calc(100vw-2.5rem)] overflow-y-auto border border-catalyst-grey/30 bg-white p-2 shadow-lg sm:w-80">
                                <x-dashboard.navigation :active="$dashboardNavigation" :show-brand="false" />
                            </div>
                        </details>
                    </div>
                </header>

                <main id="dashboard-content" tabindex="-1">
                    @yield('content')
                </main>
            </div>
        </div>
        @livewireScripts
    </body>
</html>
