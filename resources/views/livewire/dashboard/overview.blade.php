<div>
    <x-ui.container class="py-8 sm:py-10 lg:py-12">
        <div class="space-y-10 lg:space-y-14">
            <header class="max-w-3xl">
                <p class="text-sm text-catalyst-grey">Your Catalyst workspace</p>
                <h1 class="mt-5 font-display text-3xl font-semibold tracking-tight text-catalyst-black sm:text-4xl">Hi, {{ $state['user']['name'] }}</h1>
                <p class="mt-4 text-base leading-7 text-catalyst-ink/80 sm:text-lg">Track your competition registration, submission, and Summit Pass.</p>
            </header>

            <section aria-label="Dashboard summary" class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <article class="border border-catalyst-grey/30 bg-white p-5 shadow-sm">
                    <p class="text-sm text-catalyst-ink/80">Registered competition</p>
                    <p class="mt-4 font-display text-4xl font-semibold tracking-tight text-catalyst-green">{{ $state['summary']['registered_competitions'] }}</p>
                </article>

                <article class="border border-catalyst-grey/30 bg-white p-5 shadow-sm">
                    <p class="text-sm text-catalyst-ink/80">Verification pending</p>
                    <p class="mt-4 font-display text-4xl font-semibold tracking-tight text-status-warning">{{ $state['summary']['verification_pending'] }}</p>
                </article>

                <article class="border border-catalyst-grey/30 bg-white p-5 shadow-sm sm:col-span-2">
                    <p class="text-sm text-catalyst-ink/80">Upcoming event</p>
                    @if ($state['upcoming_event'])
                        <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div>
                                <h2 class="font-display text-2xl font-semibold tracking-tight text-catalyst-black">{{ $state['upcoming_event']['name'] }}</h2>
                                <p class="mt-1 text-sm text-catalyst-ink/70">{{ $state['upcoming_event']['competition'] }} · <time datetime="{{ $state['upcoming_event']['starts_at'] }}">{{ $state['upcoming_event']['date_label'] }}, {{ $state['upcoming_event']['time_label'] }}</time></p>
                            </div>
                            <p class="font-display text-2xl font-semibold text-catalyst-primary" aria-label="Time remaining">{{ $state['upcoming_event']['time_remaining'] }}</p>
                        </div>
                    @else
                        <p class="mt-4 max-w-md text-sm leading-6 text-catalyst-ink/70">There are no upcoming Catalyst milestones right now.</p>
                    @endif
                </article>
            </section>

            @if ($state['actions'] !== [])
                <section id="action-required" aria-labelledby="action-required-heading">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <h2 id="action-required-heading" class="font-display text-2xl font-medium tracking-tight">Action Required</h2>
                        <p class="text-sm leading-5 text-catalyst-grey">Complete these items to stay on track.</p>
                    </div>

                    <div class="mt-4 space-y-3">
                        @foreach ($state['actions'] as $action)
                            <article class="border border-catalyst-grey/30 border-l-4 bg-white p-4 shadow-sm sm:flex sm:items-center sm:justify-between sm:gap-6" style="border-left-color: {{ $action['accent'] }}">
                                <div>
                                    <h3 class="font-display text-xl font-semibold tracking-tight text-catalyst-black">{{ $action['title'] }}</h3>
                                    <p class="mt-2 text-sm leading-6 text-catalyst-ink/80">{{ $action['description'] }}</p>
                                </div>
                                <button class="mt-4 inline-flex w-full shrink-0 items-center justify-center bg-linear-to-b from-catalyst-primary to-catalyst-green px-4 py-3 text-sm font-medium text-white opacity-60 sm:mt-0 sm:w-auto" type="button" disabled title="This flow is not available in the prototype yet">
                                    {{ $action['cta'] }}
                                    <span class="sr-only">Unavailable until this feature is implemented</span>
                                </button>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            <section aria-labelledby="competitions-heading">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <h2 id="competitions-heading" class="font-display text-2xl font-medium tracking-tight">My Competitions</h2>
                    <p class="text-sm leading-5 text-catalyst-grey">Your registered competitions and current status.</p>
                </div>

                @if ($state['registrations'] !== [])
                    <div class="mt-4 grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach ($state['registrations'] as $registration)
                            <article class="relative min-h-52 overflow-hidden bg-linear-to-br from-catalyst-ink via-catalyst-blue to-catalyst-primary p-6 text-white">
                                <div class="absolute -right-12 -top-12 size-40 rounded-full bg-catalyst-lime/70 blur-3xl" aria-hidden="true"></div>
                                <div class="absolute -bottom-16 -left-10 size-40 rounded-full bg-catalyst-green/70 blur-3xl" aria-hidden="true"></div>
                                <div class="relative flex h-full flex-col">
                                    <p class="text-sm text-white/80">{{ $registration['code'] }} · {{ $registration['status_label'] }}</p>
                                    <h3 class="mt-3 max-w-56 font-display text-2xl font-semibold leading-tight tracking-tight">{{ $registration['name'] }}</h3>
                                    <p class="mt-3 text-sm text-white/80">Next: {{ $registration['next_stage'] }}</p>
                                    <p class="mt-1 text-sm text-white/80">{{ $registration['deadline_label'] }}</p>
                                    <button class="mt-auto w-fit pt-6 text-sm font-medium underline underline-offset-4 opacity-60" type="button" disabled title="Registration details are not available in the prototype yet">
                                        {{ $registration['cta'] }}
                                        <span class="sr-only">Unavailable until this feature is implemented</span>
                                    </button>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="mt-4 border border-dashed border-catalyst-grey/50 bg-white px-6 py-10 text-center sm:px-10">
                        <svg class="mx-auto size-8 text-catalyst-primary" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="m12 3 7 4v10l-7 4-7-4V7l7-4Zm0 5.5L8 10.7v4.6l4 2.2 4-2.2v-4.6l-4-2.2Z" stroke="currentColor" stroke-linejoin="round" stroke-width="1.5" />
                        </svg>
                        <h3 class="mt-4 font-display text-xl font-semibold tracking-tight">No competitions yet</h3>
                        <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-catalyst-ink/70">Explore Catalyst competitions and find the track that fits you.</p>
                        <a class="mt-5 inline-flex items-center justify-center bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" href="{{ route('main-event.index') }}">
                            Explore Competitions
                        </a>
                    </div>
                @endif
            </section>

            <section aria-labelledby="summit-pass-heading">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <h2 id="summit-pass-heading" class="font-display text-2xl font-medium tracking-tight">My Summit Pass</h2>
                    <p class="text-sm leading-5 text-catalyst-grey">Your Summit Pass will be available after payment verification.</p>
                </div>

                @if ($state['summit_pass'])
                    <article class="mt-4 max-w-sm border border-catalyst-grey/30 bg-white p-6 shadow-sm">
                        <p class="text-sm text-catalyst-primary">{{ $state['summit_pass']['status'] }}</p>
                        <h3 class="mt-3 font-display text-2xl font-semibold tracking-tight">{{ $state['summit_pass']['name'] }}</h3>
                        <p class="mt-2 text-sm text-catalyst-ink/70">{{ $state['summit_pass']['date'] }}</p>
                        <button class="mt-6 text-sm font-medium text-catalyst-primary underline underline-offset-4 opacity-60" type="button" disabled title="Summit Pass details are not available in the prototype yet">
                            {{ $state['summit_pass']['cta'] }}
                            <span class="sr-only">Unavailable until this feature is implemented</span>
                        </button>
                    </article>
                @else
                    <div class="mt-4 border border-catalyst-grey/30 bg-white px-6 py-10 sm:px-10">
                        <div class="max-w-xl">
                            <h3 class="font-display text-xl font-semibold tracking-tight">Your Summit Pass isn't active yet</h3>
                            <p class="mt-2 text-sm leading-6 text-catalyst-ink/70">Join the Catalyst Summit experience with Talkshow and Exhibition access.</p>
                            @if ($state['summit_sales'] === 'open')
                                <button class="mt-5 inline-flex items-center justify-center bg-linear-to-b from-catalyst-primary to-catalyst-green px-4 py-3 text-sm font-medium text-white opacity-60" type="button" disabled title="Summit Pass purchase is not available in the prototype yet">
                                    Get Summit Pass
                                    <span class="sr-only">Unavailable until purchase is implemented</span>
                                </button>
                            @else
                                <a class="mt-5 inline-flex items-center justify-center border border-catalyst-primary px-4 py-3 text-sm font-medium text-catalyst-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" href="{{ route('main-event.index') }}">
                                    View Main Event
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
            </section>

            <details class="border border-catalyst-grey/30 bg-white p-4">
                <summary class="cursor-pointer text-sm font-medium text-catalyst-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary">Prototype state</summary>
                <div class="mt-4 max-w-xs">
                    <label class="block text-sm text-catalyst-ink/80" for="dashboard-scenario">Review scenario</label>
                    <select id="dashboard-scenario" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-2 text-sm text-catalyst-ink focus:border-catalyst-primary focus:outline-none" wire:model.live="scenario">
                        @foreach ($scenarios as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </details>
        </div>
    </x-ui.container>
</div>
