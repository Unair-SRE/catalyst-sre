@props([
    'eyebrow' => 'OFFICIAL POSTER',
    'title',
    'description',
    'image',
    'imageAlt',
    'metadata' => 'Official Poster',
])

@php
    $posterId = 'poster-' . \Illuminate\Support\Str::slug($title);
@endphp

<section
    class="py-20 sm:py-24 lg:py-32"
    aria-labelledby="{{ $posterId }}-title"
    data-reveal
>
    <x-ui.container>
        <div class="grid gap-10 md:grid-cols-12 md:items-center lg:gap-16">

            {{-- Left: Editorial copy --}}
            <div class="md:col-span-5">
                <x-public.section-label>
                    {{ $eyebrow }}
                </x-public.section-label>

                <h2
                    id="{{ $posterId }}-title"
                    class="mt-5 max-w-lg font-display text-3xl font-semibold leading-tight tracking-tight text-catalyst-ink sm:text-4xl lg:text-[2.75rem]"
                >
                    {{ $title }}
                </h2>

                <p class="mt-5 max-w-xl text-sm leading-6 text-catalyst-muted sm:text-base sm:leading-7">
                    {{ $description }}
                </p>
            </div>

            {{-- Right: Poster --}}
            <div class="md:col-span-7 md:pl-4 lg:pl-8">
                <figure
                    class="overflow-hidden border border-catalyst-ink/10 bg-white"
                >
                    {{-- Poster image --}}
                    <div class="flex min-h-[24rem] items-center justify-center bg-[#F7F9F7] p-5 sm:p-7 lg:min-h-[32rem] lg:p-8">
                        <img
                            class="max-h-[38rem] w-full object-contain"
                            src="{{ asset($image) }}"
                            alt="{{ $imageAlt }}"
                            loading="lazy"
                            decoding="async"
                        >
                    </div>

                    {{-- Metadata --}}
                    <figcaption
                        class="border-t border-catalyst-ink/10 px-5 py-4 sm:px-6"
                    >
                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-catalyst-primary">
                            Official Poster
                        </p>

                        <p class="mt-1 text-sm font-medium text-catalyst-ink sm:text-base">
                            {{ $metadata }}
                        </p>
                    </figcaption>
                </figure>
            </div>

        </div>
    </x-ui.container>
</section>