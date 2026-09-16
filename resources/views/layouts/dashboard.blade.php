<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@hasSection('title')@yield('title') | @endif{{ config('app.name') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-catalyst-background font-sans text-catalyst-ink antialiased">
        <div class="min-h-screen lg:grid lg:grid-cols-[16rem_minmax(0,1fr)]">
            <aside class="border-catalyst-grey/30 border-b lg:border-r lg:border-b-0" aria-label="Dashboard navigation">
                @yield('sidebar')
            </aside>

            <main class="min-w-0">
                @yield('content')
            </main>
        </div>
    </body>
</html>
