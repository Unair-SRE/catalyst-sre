<section class="bg-white py-24">

    {{-- Header --}}
    <div class="text-center">

        <div class="flex items-center justify-center gap-2 text-[20px] font-semibold leading-5 text-catalyst-ink">
            <span>◕</span>
            <span>Event highlights</span>
        </div>


        <h2 class="mt-5 font-display text-[36px] font-semibold leading-[41px] tracking-[-0.36px] text-catalyst-ink">
            Pre-Event 2, in pictures.
        </h2>

    </div>



    {{-- Gallery --}}
    <div class="mt-12 grid h-[600px] grid-cols-[195px_1fr_195px] gap-5 overflow-hidden">


        {{-- Left --}}
        <div>
            <img
                src="{{ asset('images/pre-event-1/highlight-left.webp') }}"
                class="h-full w-full object-cover"
                alt=""
            >
        </div>



        {{-- Center --}}
        <div>
            <img
                src="{{ asset('images/pre-event-1/highlight-main.webp') }}"
                class="h-full w-full object-cover"
                alt=""
            >
        </div>



        {{-- Right --}}
        <div>
            <img
                src="{{ asset('images/pre-event-1/highlight-right.webp') }}"
                class="h-full w-full object-cover"
                alt=""
            >
        </div>


    </div>



    {{-- Caption --}}
    <p class="mx-auto mt-10 max-w-[419px] text-center font-display text-[24px] font-normal leading-[27px] text-catalyst-ink">
        A selection of moments<br>
        from the 2nd phase of Catalyst 2026.
    </p>


</section>
