<section class="px-6 py-10 sm:px-10 lg:px-20 next-section">

    <div
        class="relative overflow-hidden border border-[#00804D]/40 bg-gradient-to-b from-white to-[#E0EDED] px-8 py-10 sm:px-14 sm:py-14 next-card"
    >

        {{-- Background Number --}}
        <span
            class="absolute right-0 top-1/2 -translate-y-1/2 text-[290px] font-semibold leading-none text-white/70 next-number"
        >
            02
        </span>


        {{-- Content --}}
        <div class="relative z-10 max-w-[640px]">

            {{-- Label --}}
            <div class="flex items-center gap-2 text-sm font-semibold text-[#172126] next-item">
                <span>●</span>
                <span>Next in catalyst</span>
            </div>


            {{-- Title --}}
            <h2
                class="mt-6 font-display text-4xl font-semibold leading-tight tracking-tight text-[#1E293B] next-item sm:text-5xl"
            >
                Continue to Pre-Event 2.
            </h2>


            {{-- Description --}}
            <p
                class="mt-6 max-w-[500px] text-xl leading-8 text-[#60738F] next-item"
            >
                The first phase is complete. See how the catalyst journey continues in Pre-Event 2
            </p>


            {{-- Buttons --}}
            <div class="mt-12 flex flex-wrap gap-3 next-item">

                <a
                    href="{{ route('pre-event-2.index') }}"
                    class="inline-flex h-[50px] items-center gap-3 bg-[#00804D] px-5 text-sm font-medium text-white transition hover:bg-[#006d6a]"
                >
                    Explore Pre-Event 2
                    <span class="text-xl">→</span>
                </a>


                <a
                    href="{{ route('main-event.index') }}"
                    class="inline-flex h-[48px] items-center border border-[#00804D] px-5 text-sm font-medium text-[#00804D] transition hover:bg-[#EAF3EE]"
                >
                    View Catalyst Summit
                </a>

            </div>

        </div>

    </div>

</section>
