@extends('layouts.public')

@section('title', 'Competitions')

@section('content')
<x-ui.container class="py-14 sm:py-20 lg:py-24">
    <header class="max-w-3xl"><p class="text-xs font-semibold uppercase tracking-[0.2em] text-catalyst-primary">Catalyst competitions</p><h1 class="mt-4 font-display text-4xl font-semibold tracking-tight sm:text-6xl">Build ideas that move business forward.</h1><p class="mt-5 text-base leading-7 text-catalyst-ink/70 sm:text-lg">Explore the available competition tracks, current fees, and registration windows managed by the Catalyst committee.</p></header>
    <section class="mt-12 grid gap-5 md:grid-cols-2 xl:grid-cols-3" aria-label="Available competitions">
        @forelse($competitions as $competition)
            <article class="flex min-h-[27rem] flex-col border border-catalyst-grey/30 bg-white p-6 sm:p-7"><div class="flex items-start justify-between gap-4"><p class="text-xs font-semibold tracking-[0.2em] text-catalyst-primary">{{ $competition->code->value }}</p><span class="text-xs font-semibold {{ $competition->acceptsRegistration() ? 'text-status-success-ink' : 'text-status-error-ink' }}">{{ $competition->acceptsRegistration() ? 'REGISTRATION OPEN' : 'REGISTRATION CLOSED' }}</span></div><h2 class="mt-7 font-display text-2xl font-semibold">{{ $competition->name }}</h2><p class="mt-4 text-sm leading-6 text-catalyst-ink/70">{{ $competition->description ?: 'Additional competition details will be announced by the committee.' }}</p><dl class="mt-7 border-t border-catalyst-grey/30 pt-5 text-sm"><div class="flex justify-between gap-4"><dt class="text-catalyst-muted">Registration fee</dt><dd class="font-semibold">IDR {{ number_format((float) $competition->registration_fee, 0, ',', '.') }}</dd></div>@if($competition->registration_end_at)<div class="mt-3 flex justify-between gap-4"><dt class="text-catalyst-muted">Closes</dt><dd class="text-right font-medium">{{ $competition->registration_end_at->format('d M Y, H:i') }} WIB</dd></div>@endif</dl>
                @auth<a class="mt-auto inline-flex justify-center bg-catalyst-primary px-4 py-3 text-sm font-medium text-white" href="{{ route('dashboard.registration.show', strtolower($competition->code->value)) }}">{{ $competition->acceptsRegistration() ? 'Review Registration' : 'View Details' }}</a>@else<a class="mt-auto inline-flex justify-center bg-catalyst-primary px-4 py-3 text-sm font-medium text-white" href="{{ route('register') }}">Create account to register</a>@endauth
            </article>
        @empty
            <div class="col-span-full border border-catalyst-grey/30 bg-white px-6 py-14 text-center"><h2 class="font-display text-2xl font-semibold">Competition information is coming soon</h2><p class="mt-3 text-sm text-catalyst-muted">Please check back after the committee publishes the competition schedule.</p></div>
        @endforelse
    </section>
</x-ui.container>
@endsection
