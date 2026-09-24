@props(['external' => false])

<img
    {{ $attributes->class($external ? 'size-4 shrink-0' : 'size-5 shrink-0') }}
    src="{{ asset($external ? 'images/icon/arrow-icon-diagonal.svg' : 'images/icon/arrow-icon-right.svg') }}"
    width="{{ $external ? 16 : 20 }}"
    height="{{ $external ? 16 : 20 }}"
    alt=""
    aria-hidden="true"
>
