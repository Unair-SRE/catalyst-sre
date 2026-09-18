@props(['label', 'tone' => 'neutral'])

@php
    $classes = match ($tone) {
        'success' => 'bg-status-success/10 text-status-success-ink',
        'warning' => 'bg-status-warning/10 text-status-warning-ink',
        'error' => 'bg-status-error/10 text-status-error-ink',
        'info' => 'bg-status-info/10 text-status-info-ink',
        default => 'bg-catalyst-grey/15 text-catalyst-ink/75',
    };
@endphp

<span {{ $attributes->class("inline-flex items-center px-2.5 py-1 text-xs font-medium {$classes}") }}>{{ $label }}</span>
