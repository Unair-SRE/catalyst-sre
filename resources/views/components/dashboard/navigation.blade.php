@props(['active' => 'overview', 'showBrand' => true])

<nav class="flex h-full min-h-0 flex-col" aria-label="Participant dashboard">
    @if ($showBrand)
        <a class="flex min-h-24 items-center gap-3 px-5 font-display text-sm font-semibold tracking-wide focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-catalyst-primary" href="{{ route('dashboard.index') }}">
            <span class="grid size-9 place-items-center rounded-full bg-catalyst-primary font-sans text-sm font-bold text-white" aria-hidden="true">C</span>
            <span>Catalyst 2026</span>
        </a>
    @endif

    <div class="flex flex-1 flex-col gap-6 px-3 pb-5 {{ $showBrand ? '' : 'pt-3' }}">
        <div class="border-catalyst-grey/30 border-b pb-4">
            <p class="px-2 text-xs font-medium uppercase tracking-widest text-catalyst-grey">Overview</p>
            <ul class="mt-2">
                <li>
                    <a class="flex items-center gap-3 rounded-md px-2 py-2 text-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary {{ $active === 'overview' ? 'font-semibold text-catalyst-ink' : 'font-medium text-catalyst-grey' }}" href="{{ route('dashboard.index') }}" @if ($active === 'overview') aria-current="page" @endif>
                        <svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M3.5 3.5h5v5h-5zm8 0h5v5h-5zm-8 8h5v5h-5zm8 0h5v5h-5z" stroke="currentColor" stroke-linejoin="round" stroke-width="1.5" />
                        </svg>
                        Dashboard
                    </a>
                </li>
            </ul>
        </div>

        <div class="border-catalyst-grey/30 border-b pb-4">
            <p class="px-2 text-xs font-medium uppercase tracking-widest text-catalyst-grey">Competition</p>
            <ul class="mt-2 space-y-1">
                <li>
                    <a class="flex items-center gap-3 rounded-md px-2 py-2 text-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary {{ $active === 'registration' ? 'font-semibold text-catalyst-ink' : 'font-medium text-catalyst-grey' }}" href="{{ route('dashboard.registration.index') }}" @if ($active === 'registration') aria-current="page" @endif>
                        <svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M5 2.5h7l3 3v12H5zM12 2.5v3h3M7.5 10h5M7.5 13h5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" />
                        </svg>
                        Registration
                    </a>
                </li>
                <li>
                    <span class="flex items-center gap-3 px-2 py-2 text-sm text-catalyst-grey" aria-disabled="true">
                        <svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M10 3v9m0 0 3.5-3.5M10 12 6.5 8.5M4 15.5h12" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" />
                        </svg>
                        Submission
                        <span class="sr-only">Coming soon</span>
                    </span>
                </li>
            </ul>
        </div>

        <div class="border-catalyst-grey/30 border-b pb-4">
            <p class="px-2 text-xs font-medium uppercase tracking-widest text-catalyst-grey">Exhibition</p>
            <ul class="mt-2">
                <li>
                    <span class="flex items-center gap-3 px-2 py-2 text-sm text-catalyst-grey" aria-disabled="true">
                        <svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M3 6.5h14v8H3zM6 6.5V4.75A1.75 1.75 0 0 1 7.75 3h4.5A1.75 1.75 0 0 1 14 4.75V6.5M7 11h6" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" />
                        </svg>
                        Summit Pass
                        <span class="sr-only">Coming soon</span>
                    </span>
                </li>
            </ul>
        </div>

        <div>
            <p class="px-2 text-xs font-medium uppercase tracking-widest text-catalyst-grey">Others</p>
            <ul class="mt-2">
                <li>
                    <span class="flex items-center gap-3 px-2 py-2 text-sm text-catalyst-grey" aria-disabled="true">
                        <svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <circle cx="10" cy="6.5" r="3" stroke="currentColor" stroke-width="1.5" />
                            <path d="M4.5 17c.7-2.7 2.5-4 5.5-4s4.8 1.3 5.5 4" stroke="currentColor" stroke-linecap="round" stroke-width="1.5" />
                        </svg>
                        Profile
                        <span class="sr-only">Coming soon</span>
                    </span>
                </li>
            </ul>
        </div>

        <div class="mt-auto pt-4">
            <button class="flex w-full items-center gap-3 rounded-md px-2 py-2 text-left text-sm text-status-error opacity-60" type="button" disabled title="Logout will be available with authentication">
                <svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                    <path d="M8 3H4.5v14H8m4-10 3 3-3 3m3-3H8" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" />
                </svg>
                Logout
                <span class="sr-only">Unavailable until authentication is implemented</span>
            </button>
        </div>
    </div>
</nav>
