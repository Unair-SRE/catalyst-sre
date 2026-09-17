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
    <body class="bg-catalyst-background font-sans text-catalyst-ink antialiased">
        @php($dashboardNavigation = match (true) {
            request()->routeIs('dashboard.registration.*') => 'registration',
            request()->routeIs('dashboard.submission.*') => 'submission',
            request()->routeIs('dashboard.summit-pass.*') => 'summit-pass',
            request()->routeIs('dashboard.profile.*') => 'profile',
            default => 'overview',
        })
        <div class="min-h-screen lg:grid lg:grid-cols-[16rem_minmax(0,1fr)]">
            <aside class="hidden border-catalyst-grey/30 border-r bg-white lg:block" aria-label="Dashboard navigation">
                <x-dashboard.navigation :active="$dashboardNavigation" />
            </aside>

            <div class="min-w-0">
                <header class="border-catalyst-grey/30 border-b bg-white lg:hidden">
                    <div class="flex min-h-16 items-center justify-between px-5 sm:px-6">
                        <a class="inline-flex items-center gap-3 font-display text-sm font-semibold tracking-wide focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-catalyst-primary" href="{{ route('dashboard.index') }}">
                            <span class="grid size-9 place-items-center rounded-full bg-catalyst-primary font-sans text-sm font-bold text-white" aria-hidden="true">C</span>
                            <span>Catalyst 2026</span>
                        </a>

                        <details class="relative">
                            <summary class="flex cursor-pointer list-none items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-catalyst-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary">
                                Menu
                                <svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="m5 7.5 5 5 5-5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" />
                                </svg>
                            </summary>
                            <div class="absolute right-0 top-full z-20 mt-2 w-72 rounded-lg border border-catalyst-grey/30 bg-white p-2 shadow-lg sm:w-80">
                                <x-dashboard.navigation :active="$dashboardNavigation" :show-brand="false" />
                            </div>
                        </details>
                    </div>
                </header>

                <main>
                    @yield('content')
                </main>
            </div>
        </div>
        @livewireScripts
    </body>
</html>
