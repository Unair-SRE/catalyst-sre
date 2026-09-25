<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@hasSection('title')@yield('title') | @endif{{ config('app.name') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-white font-display text-catalyst-ink antialiased">
        <main class="min-h-screen bg-white lg:grid lg:grid-cols-2">
            <section class="relative flex min-h-screen min-w-0 items-center px-5 py-10 sm:px-10 lg:px-12 lg:py-16 xl:px-[6.25rem] xl:py-[6.25rem]">
                <div class="relative flex w-full items-center justify-center lg:min-h-[calc(100vh-8rem)] lg:border lg:border-neutral-200/80 xl:min-h-[calc(100vh-12.5rem)]">
                    <div class="pointer-events-none absolute inset-x-0 bottom-0 hidden h-36 bg-gradient-to-b from-white/0 to-stone-50/80 lg:block" aria-hidden="true"></div>

                    <div class="relative z-10 w-full max-w-96 py-0 lg:py-8">
                        @yield('content')
                    </div>
                </div>
            </section>

            <aside class="relative hidden min-h-screen overflow-hidden bg-catalyst-neutral lg:block" aria-hidden="true">
                <img
                    class="absolute inset-0 size-full object-cover object-center"
                    src="{{ asset('images/brand/footer-image.webp') }}"
                    width="1440"
                    height="700"
                    alt=""
                    decoding="async"
                    fetchpriority="high"
                >
            </aside>
        </main>
    </body>
</html>
