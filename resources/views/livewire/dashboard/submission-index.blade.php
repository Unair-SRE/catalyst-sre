<div>
    <x-ui.container class="py-8 sm:py-10 lg:py-14">
        <div class="space-y-10 lg:space-y-14">
            <header class="dashboard-page-heading max-w-3xl">
                <p class="text-xs font-medium tracking-wide text-catalyst-muted">Competition workspace</p>
                <h1 class="mt-5 font-display text-3xl font-medium leading-tight tracking-tight text-catalyst-ink sm:text-4xl xl:text-5xl">Submission</h1>
                <p class="mt-4 text-base leading-7 text-catalyst-ink/80 sm:text-lg">Manage your competition submissions and deadlines.</p>
            </header>

            @if ($competitions === [])
                <section class="border border-dashed border-catalyst-grey/50 bg-white px-6 py-10 text-center sm:px-10" aria-labelledby="empty-submission-heading">
                    <svg class="mx-auto size-8 text-catalyst-primary" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M12 3v11m0 0 4-4m-4 4-4-4M5 18.5h14" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" />
                    </svg>
                    <h2 id="empty-submission-heading" class="mt-4 font-display text-xl font-semibold tracking-tight">No submissions yet</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-catalyst-ink/70">Your competition submissions will appear here after you register and complete verification.</p>
                    <a class="mt-5 inline-flex min-h-11 items-center justify-center bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" href="{{ route('dashboard.registration.index') }}">View Registration</a>
                </section>
            @else
                <section aria-label="Competition submissions">
                    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                        @foreach ($competitions as $item)
                            <article class="competition-card flex min-h-72 flex-col gap-5 border border-catalyst-grey/30 border-t-2 border-t-catalyst-primary bg-catalyst-neutral p-6">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="text-xs font-medium tracking-widest text-catalyst-muted">{{ $item['competition']['short_name'] }}</p>
                                        <h2 class="mt-3 font-display text-2xl font-semibold tracking-tight text-catalyst-black">{{ $item['competition']['name'] }}</h2>
                                    </div>
                                    <x-dashboard.status-pill :label="$item['access']['label']" :tone="$item['access']['tone']" />
                                </div>

                                <div class="mt-6 border-catalyst-grey/30 border-t pt-5">
                                    <p class="text-sm font-medium text-catalyst-ink">{{ $item['stage']['name'] }}</p>
                                    @if ($item['registration_status'] !== 'VERIFIED')
                                        <p class="mt-2 text-sm leading-6 text-catalyst-ink/70">Registration verification required before submission becomes available.</p>
                                    @elseif ($item['stage_state'] === 'upcoming')
                                        <p class="mt-2 text-sm leading-6 text-catalyst-ink/70">Opens <time datetime="{{ $item['stage']['open_at'] }}">{{ $item['stage']['open_label'] }}</time>.</p>
                                    @elseif ($item['submitted_label'])
                                        <p class="mt-2 text-sm leading-6 text-catalyst-ink/70">Submitted <time datetime="{{ $item['submitted_at'] }}">{{ $item['submitted_label'] }}</time>.</p>
                                    @else
                                        <p class="mt-2 text-sm leading-6 text-catalyst-ink/70">Deadline <time datetime="{{ $item['stage']['deadline_at'] }}">{{ $item['stage']['deadline_label'] }}</time>.</p>
                                    @endif
                                </div>

                                <dl class="mt-5 grid grid-cols-2 gap-3 text-sm">
                                    <div>
                                        <dt class="text-catalyst-muted">Registration</dt>
                                        <dd class="mt-1"><x-dashboard.status-pill :label="$item['registration']['label']" :tone="$item['registration']['tone']" /></dd>
                                    </div>
                                    <div>
                                        <dt class="text-catalyst-muted">Stage</dt>
                                        <dd class="mt-1"><x-dashboard.status-pill :label="$item['stage_lifecycle']['label']" :tone="$item['stage_lifecycle']['tone']" /></dd>
                                    </div>
                                    <div>
                                        <dt class="text-catalyst-muted">Submission</dt>
                                        <dd class="mt-1"><x-dashboard.status-pill :label="$item['submission']['label']" :tone="$item['submission']['tone']" /></dd>
                                    </div>
                                </dl>

                                <a class="mt-auto inline-flex min-h-11 items-center justify-center bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary sm:self-start" href="{{ $item['href'] }}">{{ $item['cta'] }}</a>
                            </article>
                        @endforeach
                    </div>
                </section>
            @endif

            <details class="border border-catalyst-grey/30 bg-white p-4">
                <summary class="cursor-pointer text-sm font-medium text-catalyst-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary">Prototype state</summary>
                <div class="mt-4 max-w-xs">
                    <label class="block text-sm text-catalyst-ink/80" for="submission-index-scenario">Review scenario</label>
                    <select id="submission-index-scenario" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-2 text-sm text-catalyst-ink focus:border-catalyst-primary focus:outline-none" wire:model.live="scenario">
                        @foreach ($scenarios as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </details>
        </div>
    </x-ui.container>
</div>
