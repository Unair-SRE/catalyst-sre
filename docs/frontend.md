# Frontend Foundation v1

## Stack

- Laravel 13 server-rendered Blade
- Tailwind CSS 4 using CSS-first configuration in `resources/css/app.css`
- Vite entry points in `resources/css/app.css` and `resources/js/app.js`
- No React, Vue, Inertia, Livewire, Alpine, or other frontend framework without team agreement

## Views and layouts

- `layouts/public.blade.php` is for Home, Pre-Event 1, Pre-Event 2, and Main Event.
- `layouts/auth.blade.php` is reserved for future authentication pages.
- `layouts/dashboard.blade.php` is for all Participant Dashboard pages; it supplies a sidebar and main-content shell only.
- Public pages live in `pages/home`, `pages/pre-event-1`, `pages/pre-event-2`, and `pages/main-event`.
- Participant Dashboard pages live in `dashboard` and must use the dashboard layout.
- Public-page ownership is organized by feature: Home, Pre-Event 1, Pre-Event 2, and Main Event developers should keep feature-specific work in their own page directory.

Use layouts with Blade inheritance, for example `@extends('layouts.public')`, `@section('title', 'Page title')`, and `@section('content')`. Layouts provide `title` and `content` sections; the dashboard layout also provides `sidebar`.

## Tokens

Use Tailwind token utilities rather than repeating brand hex values:

- `catalyst-primary`, `catalyst-green`, `catalyst-blue`, `catalyst-lime`, `catalyst-ink`, `catalyst-black`, `catalyst-grey`, `catalyst-background`
- `status-error`, `status-warning`, `status-success`, `status-info`
- `font-display` for PP Mori with Inter fallback; `font-sans` for Inter UI text

Inter is loaded by the existing Bunny/Vite integration. PP Mori is a licensed font and is not included in this repository; add its licensed webfont assets before treating it as guaranteed at runtime.

## Routes

- `home` → `/`
- `pre-event-1.index` → `/pre-event-1`
- `pre-event-2.index` → `/pre-event-2`
- `main-event.index` → `/main-event`
- `dashboard.index` → `/dashboard`

Generate internal URLs with `route()` and follow this `feature.index` convention for future top-level feature pages.

## Component reuse

`<x-ui.container>` is the only general primitive in Foundation v1. Add a component only after a real design pattern appears in more than one place. Keep public-specific and dashboard-specific components in their respective component namespaces when they diverge.
