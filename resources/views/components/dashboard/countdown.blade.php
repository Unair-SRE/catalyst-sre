@props(['target'])

<catalyst-countdown target="{{ \Carbon\CarbonImmutable::parse($target, 'Asia/Jakarta')->toIso8601String() }}" class="block" role="timer" aria-label="Time remaining until event" aria-live="off">
    <div class="flex items-start gap-2 sm:gap-3" aria-hidden="true">
        @foreach (['Days', 'Hours', 'Minutes', 'Seconds'] as $unit)
            @unless ($loop->first)<span class="pt-1 font-display text-2xl text-catalyst-primary/40 sm:text-3xl">:</span>@endunless
            <div class="min-w-0 text-center">
                <span data-countdown-unit class="block font-display text-3xl font-medium tabular-nums tracking-tight text-catalyst-primary sm:text-4xl" wire:ignore>00</span>
                <span class="mt-2 block text-[0.65rem] font-medium tracking-wide text-catalyst-muted sm:text-xs">{{ $unit }}</span>
            </div>
        @endforeach
    </div>
</catalyst-countdown>
