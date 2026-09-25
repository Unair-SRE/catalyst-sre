@props(['title', 'description' => null])

<section {{ $attributes->class(['w-full']) }}>
    <a
        class="inline-flex focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-catalyst-primary focus-visible:ring-offset-4"
        href="{{ route('home') }}"
        aria-label="Go back"
        title="Back"
        data-fallback-url="{{ route('home') }}"
        onclick="event.preventDefault(); document.referrer && window.history.length > 1 ? window.history.back() : window.location.assign(this.dataset.fallbackUrl)"
    >
        <img class="h-12 w-[26px] object-contain" src="{{ asset('images/brand/catalyst-mark.png') }}" width="26" height="48" alt="">
    </a>

    <h1 class="mt-4 text-2xl font-semibold leading-8 tracking-tight text-zinc-900">{{ $title }}</h1>
    @if ($description)
        <p class="mt-3 max-w-sm text-sm leading-6 text-catalyst-muted">{{ $description }}</p>
    @endif
    {{ $slot }}
</section>