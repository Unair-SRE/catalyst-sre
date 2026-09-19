<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@hasSection('title')@yield('title') | @endif{{ config('app.name') }}</title>

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="public-site flex min-h-screen flex-col bg-catalyst-background font-sans text-catalyst-ink antialiased">
        <a class="sr-only z-50 bg-white px-4 py-3 font-medium text-catalyst-primary focus:not-sr-only focus:fixed focus:left-4 focus:top-4" href="#main-content">
            Skip to content
        </a>

        <x-public.navbar />

        <main id="main-content" class="flex-1" tabindex="-1">
            @yield('content')
        </main>

        <x-public.footer />
    </body>
</html>
