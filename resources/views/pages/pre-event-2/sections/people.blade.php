<section class="bg-white px-6 py-24 sm:px-10 lg:px-[100px]">

    <div class="mx-auto max-w-[1240px]">


        {{-- Header --}}
        <div class="mb-14">

            <div class="flex items-center gap-2 text-sm font-medium text-catalyst-ink">
                <span>◕</span>
                <span>People of catalyst</span>
            </div>


            <h2 class="mt-6 font-display text-[32px] font-semibold leading-none tracking-[-0.36px] text-catalyst-ink">
                Speakers, Judges, and contributors.
            </h2>

        </div>



        {{-- People Grid --}}
        <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">


            @php
                $people = [
                    [
                        'image' => 'images/pre-event-1/person-1.webp',
                        'type' => 'Speaker',
                    ],
                    [
                        'image' => 'images/pre-event-1/person-2.webp',
                        'type' => 'Judge',
                    ],
                    [
                        'image' => 'images/pre-event-1/person-3.webp',
                        'type' => 'Mentor',
                    ],
                    [
                        'image' => 'images/pre-event-1/person-4.webp',
                        'type' => 'Moderator',
                    ],
                ];
            @endphp



            @foreach($people as $person)

                <div>


                    {{-- Image --}}
                    <div class="aspect-[295/386] overflow-hidden bg-catalyst-neutral">

                        <img
                            src="{{ asset($person['image']) }}"
                            alt="{{ $person['type'] }}"
                            class="h-full w-full object-cover"
                        >

                    </div>



                    {{-- Badge --}}
                    <div class="mt-4 inline-flex bg-[#D8EFE4] px-2 py-1 text-sm font-medium text-[#00804D]">
                        {{ $person['type'] }}
                    </div>



                    {{-- Name --}}
                    <h3 class="mt-5 font-display text-base font-semibold text-catalyst-ink">
                        To Be Announced
                    </h3>



                    {{-- Role --}}
                    <p class="mt-2 text-sm text-catalyst-muted">
                        Role beliau
                    </p>


                </div>


            @endforeach


        </div>


    </div>

</section>
