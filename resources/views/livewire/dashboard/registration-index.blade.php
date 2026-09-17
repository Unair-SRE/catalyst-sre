<div>
    <x-ui.container class="py-8 sm:py-10 lg:py-12">
        <div class="space-y-10 lg:space-y-14">
            <header class="max-w-3xl">
                <p class="text-sm text-catalyst-grey">Competition workspace</p>
                <h1 class="mt-5 font-display text-3xl font-semibold tracking-tight text-catalyst-black sm:text-4xl">Registration</h1>
                <p class="mt-4 text-base leading-7 text-catalyst-ink/80 sm:text-lg">Manage your competition registrations and team information.</p>
            </header>

            <section aria-label="Competition registrations">
                <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($competitions as $competition)
                        <article class="flex min-h-80 flex-col border border-catalyst-grey/30 bg-white shadow-sm">
                            <div class="relative min-h-36 overflow-hidden bg-linear-to-br from-catalyst-ink via-catalyst-blue to-catalyst-primary p-5 text-white">
                                <div class="absolute -right-10 -top-12 size-36 rounded-full blur-3xl" style="background-color: {{ $competition['poster_accent'] }}" aria-hidden="true"></div>
                                <div class="relative">
                                    <p class="text-xs font-medium tracking-widest text-white/75">{{ $competition['short_name'] }}</p>
                                    <p class="mt-6 font-display text-2xl font-semibold tracking-tight">{{ $competition['name'] }}</p>
                                </div>
                            </div>
                            <div class="flex flex-1 flex-col p-5">
                                @if ($competition['registered'])
                                    <p class="text-sm text-catalyst-ink/75">Team: {{ $competition['team_name'] }}</p>
                                    <dl class="mt-5 grid gap-3 sm:grid-cols-2 md:grid-cols-1">
                                        <div>
                                            <dt class="text-xs text-catalyst-grey">Registration</dt>
                                            <dd class="mt-1"><x-dashboard.status-pill :label="$competition['registration']['label']" :tone="$competition['registration']['tone']" /></dd>
                                        </div>
                                        <div>
                                            <dt class="text-xs text-catalyst-grey">Payment</dt>
                                            <dd class="mt-1"><x-dashboard.status-pill :label="$competition['payment']['label']" :tone="$competition['payment']['tone']" /></dd>
                                        </div>
                                    </dl>
                                @else
                                    <p class="text-sm leading-6 text-catalyst-ink/70">Start a team registration when you are ready to join this competition.</p>
                                @endif
                                <a class="mt-auto inline-flex min-h-11 items-center justify-center bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" href="{{ $competition['cta_route'] }}">
                                    {{ $competition['cta_label'] }}
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>

            <details class="border border-catalyst-grey/30 bg-white p-4">
                <summary class="cursor-pointer text-sm font-medium text-catalyst-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary">Prototype state</summary>
                <div class="mt-4 max-w-xs">
                    <label class="block text-sm text-catalyst-ink/80" for="registration-index-scenario">Review scenario</label>
                    <select id="registration-index-scenario" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-2 text-sm text-catalyst-ink focus:border-catalyst-primary focus:outline-none" wire:model.live="scenario">
                        @foreach ($scenarios as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </details>
        </div>
    </x-ui.container>
</div>
