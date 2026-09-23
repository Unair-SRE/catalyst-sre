@props(['title', 'description' => null])

<section {{ $attributes->class(['w-full max-w-md border border-catalyst-grey/30 bg-white p-6 shadow-sm sm:p-8']) }}>
    <a class="inline-flex items-center gap-3 font-display text-sm font-semibold tracking-wide" href="{{ route('home') }}">
        <span class="grid size-10 place-items-center rounded-full bg-catalyst-primary font-sans font-bold text-white">C</span>
        Catalyst 2026
    </a>
    <h1 class="mt-8 font-display text-3xl font-semibold tracking-tight">{{ $title }}</h1>
    @if ($description)<p class="mt-3 text-sm leading-6 text-catalyst-ink/70">{{ $description }}</p>@endif
    {{ $slot }}
</section>
