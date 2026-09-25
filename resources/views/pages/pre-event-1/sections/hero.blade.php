<section class="relative min-h-screen overflow-hidden">

    {{-- Background Image --}}
    <img
        src="{{ asset('images/pre-event-1/hero.webp') }}"
        alt="Catalyst Summit Pre Event"
        class="hero-image absolute inset-0 h-full w-full object-cover"
    >


    {{-- Overlay --}}
    <div class="absolute inset-0 bg-black/45"></div>



    {{-- Main Hero Content --}}
    <div class="hero-content relative z-10 mx-auto flex min-h-screen w-full max-w-[1240px] items-end px-6 pb-[170px] sm:px-10 lg:px-0">


        <div class="ml-auto grid w-full grid-cols-[120px_1fr] gap-10 text-white lg:w-[900px]">


            {{-- Left Label --}}
            <div>
                <p class="mt-3 whitespace-nowrap text-sm font-medium tracking-[0.25em]">
                    Pre-Event 1
                </p>
            </div>



            {{-- Right Content --}}
            <div>

                <h1 class="max-w-[900px] text-4xl font-normal leading-[1.1] sm:text-5xl lg:text-[36px]">
                    Unite for a transformative experience the heart of Catalyst Summit awaits.
                </h1>


                <p class="mt-6 max-w-[750px] text-base leading-relaxed text-white/80">
                    Join us for a life-changing experience the essence of the Catalyst Summit awaits you.
                </p>


            </div>


        </div>


    </div>




    {{-- Bottom Navigation --}}
    <div class="absolute bottom-12 left-1/2 z-20 w-[calc(100%-200px)] -translate-x-1/2 text-white">


        {{-- Line --}}
        <div class="border-t border-white/40"></div>


        {{-- Scroll --}}
        <div class="hero-scroll">
            <div class="mt-5 flex items-center justify-between">


                <span class="text-4xl font-light">
                    ↓
                </span>


                <span class="text-base font-medium">
                    Scroll to explore
                </span>


            </div>
        </div>

    </div>


</section>
