@props([
    'stacked' => true,
    'adaptive' => false,
])

<span {{ $attributes->class(['inline-flex items-center gap-3']) }}>
    <img
        class="h-12 w-[26px] shrink-0"
        src="{{ asset('images/brand/catalyst-mark.png') }}"
        width="26"
        height="48"
        alt=""
        aria-hidden="true"
    >
    <span class="font-display text-base font-semibold leading-5 tracking-wide">
        @if ($stacked)
            <span @class(['block', 'public-navbar__wordmark-primary' => $adaptive, 'text-catalyst-ink' => ! $adaptive])>Catalyst</span>
            <span @class(['block', 'public-navbar__wordmark-accent' => $adaptive, 'text-catalyst-primary' => ! $adaptive])>Summit</span>
        @else
            <span class="text-catalyst-ink">Catalyst Summit</span>
        @endif
    </span>
</span>
