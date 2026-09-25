<p {{ $attributes->class('inline-flex items-center gap-2 font-display text-xs font-semibold tracking-[0.04em] text-catalyst-ink sm:text-sm') }}>
    <img class="size-4 shrink-0" src="{{ asset('images/icon/leaf-icon-eyebrow.svg') }}" width="16" height="16" alt="" aria-hidden="true">
    <span>{{ $slot }}</span>
</p>
