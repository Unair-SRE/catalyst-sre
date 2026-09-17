<div>
    <x-ui.container class="py-8 sm:py-10 lg:py-12">
        <div class="space-y-8 lg:space-y-10">
            <header class="max-w-3xl">
                <a class="inline-flex min-h-11 items-center gap-2 text-sm font-medium text-catalyst-ink/75 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" href="{{ route('dashboard.registration.index') }}">
                    <svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m11.5 4-6 6 6 6M6 10h9" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" /></svg>
                    Back to Registration
                </a>
                <p class="mt-6 text-sm font-medium tracking-widest text-catalyst-grey">{{ $state['competition']['short_name'] }}</p>
                <h1 class="mt-2 font-display text-3xl font-semibold tracking-tight text-catalyst-black sm:text-4xl">{{ $state['competition']['name'] }}</h1>
                <p class="mt-4 text-base leading-7 text-catalyst-ink/80 sm:text-lg">Review and complete your registration details before submission.</p>
            </header>

            <section aria-label="Registration status" class="grid gap-px overflow-hidden border border-catalyst-grey/30 bg-catalyst-grey/30 sm:grid-cols-3">
                <div class="bg-white p-5"><p class="text-sm text-catalyst-ink/75">Registration</p><div class="mt-3"><x-dashboard.status-pill :label="$state['registration']['label']" :tone="$state['registration']['tone']" /></div></div>
                <div class="bg-white p-5"><p class="text-sm text-catalyst-ink/75">Payment</p><div class="mt-3"><x-dashboard.status-pill :label="$state['payment']['label']" :tone="$state['payment']['tone']" /></div></div>
                <div class="bg-white p-5"><p class="text-sm text-catalyst-ink/75">Submission</p><div class="mt-3"><x-dashboard.status-pill :label="$state['submission']['label']" :tone="$state['submission']['tone']" /></div></div>
            </section>

            @if ($state['notice'])
                <section class="border-l-4 bg-white p-5 shadow-sm" style="border-left-color: var(--color-status-{{ $state['notice']['tone'] }})" aria-live="polite">
                    <h2 class="font-display text-xl font-semibold tracking-tight">{{ $state['notice']['title'] }}</h2>
                    <p class="mt-2 text-sm leading-6 text-catalyst-ink/75">{{ $state['notice']['description'] }}</p>
                    @if (isset($state['notice']['detail']))
                        <p class="mt-4 border border-catalyst-grey/30 p-4 text-sm leading-6 text-catalyst-ink/80">{{ $state['notice']['detail'] }}</p>
                    @endif
                    @if ($state['registration_status'] === 'REVISION_REQUIRED')
                        <button class="mt-4 bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="updateRegistration">Update Registration</button>
                    @elseif ($state['registration_status'] === 'VERIFIED')
                        <button class="mt-4 bg-catalyst-primary px-4 py-3 text-sm font-medium text-white opacity-60" type="button" disabled title="Submission is not available in this prototype">Go to Submission<span class="sr-only">Unavailable until submission is implemented</span></button>
                    @elseif ($state['registration_status'] === 'REJECTED')
                        <a class="mt-4 inline-flex border border-catalyst-primary px-4 py-3 text-sm font-medium text-catalyst-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" href="{{ $state['contact_url'] }}">Contact Catalyst</a>
                    @endif
                </section>
            @endif

            <section aria-labelledby="flow-heading" class="border border-catalyst-grey/30 bg-white p-5 shadow-sm">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <h2 id="flow-heading" class="font-display text-xl font-semibold tracking-tight">Registration Flow</h2>
                    <p class="text-sm text-catalyst-grey">Review your details before submitting.</p>
                </div>
                <ol class="mt-5 flex gap-3 overflow-x-auto pb-2" aria-label="Registration progress">
                    @foreach (['Choose Competition', 'Confirm Eligibility', 'Team Data', 'Member Data', 'Registration Folder', 'Payment', 'Review'] as $step)
                        <li class="flex min-w-28 flex-1 items-start gap-2 text-xs leading-5 text-catalyst-ink/75 sm:min-w-0">
                            <span class="grid size-6 shrink-0 place-items-center rounded-full bg-catalyst-primary text-xs font-semibold text-white">{{ $loop->iteration }}</span>
                            <span>{{ $step }}</span>
                        </li>
                    @endforeach
                </ol>
            </section>

            <form class="space-y-6" wire:submit="openConfirmation">
                <fieldset class="border border-catalyst-grey/30 bg-white p-5 shadow-sm sm:p-6" @disabled(! $state['form_editable'])>
                    <legend class="px-1 font-display text-xl font-semibold tracking-tight">Eligibility</legend>
                    <p class="mt-3 text-sm leading-6 text-catalyst-ink/75">{{ $state['competition']['eligibility'] }}</p>
                    <label class="mt-5 flex items-start gap-3 text-sm leading-6 text-catalyst-ink/80"><input class="mt-1 size-4 accent-catalyst-primary" type="checkbox" wire:model="form.eligible"><span>I confirm that our team meets the eligibility requirements for this competition.</span></label>
                    @error('form.eligible') <p class="mt-2 text-sm text-status-error">{{ $message }}</p> @enderror
                </fieldset>

                <fieldset class="border border-catalyst-grey/30 bg-white p-5 shadow-sm sm:p-6" @disabled(! $state['form_editable'])>
                    <legend class="px-1 font-display text-xl font-semibold tracking-tight">Team Data</legend>
                    <div class="mt-5 grid gap-5 sm:grid-cols-2">
                        <label class="block text-sm font-medium">Team Name<input class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none disabled:bg-catalyst-grey/10" type="text" wire:model="form.team_name">@error('form.team_name') <span class="mt-1 block text-sm text-status-error">{{ $message }}</span> @enderror</label>
                        <label class="block text-sm font-medium">Institution / School / University<input class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none disabled:bg-catalyst-grey/10" type="text" wire:model="form.institution">@error('form.institution') <span class="mt-1 block text-sm text-status-error">{{ $message }}</span> @enderror</label>
                    </div>
                </fieldset>

                <fieldset class="border border-catalyst-grey/30 bg-white p-5 shadow-sm sm:p-6" @disabled(! $state['form_editable'])>
                    <legend class="px-1 font-display text-xl font-semibold tracking-tight">Member Data</legend>
                    <p class="mt-3 text-sm leading-6 text-catalyst-ink/75">Teams consist of 2–3 people total, including the Captain.</p>
                    <div class="mt-5 space-y-5">
                        @foreach ($form['members'] as $index => $member)
                            <section class="border border-catalyst-grey/30 p-4" aria-labelledby="member-{{ $index }}-heading">
                                <h3 id="member-{{ $index }}-heading" class="font-medium">{{ $member['role'] }}</h3>
                                <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                                    <label class="block text-sm">Legal Name<input class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none disabled:bg-catalyst-grey/10" type="text" wire:model="form.members.{{ $index }}.legal_name"></label>
                                    <label class="block text-sm">Email<input class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none disabled:bg-catalyst-grey/10" type="email" wire:model="form.members.{{ $index }}.email"></label>
                                    <label class="block text-sm">WhatsApp<input class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none disabled:bg-catalyst-grey/10" type="text" wire:model="form.members.{{ $index }}.whatsapp"></label>
                                    <label class="block text-sm">Student ID <span class="text-catalyst-grey">(optional)</span><input class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none disabled:bg-catalyst-grey/10" type="text" wire:model="form.members.{{ $index }}.student_id"></label>
                                </div>
                            </section>
                        @endforeach
                    </div>
                </fieldset>

                <fieldset class="border border-catalyst-grey/30 bg-white p-5 shadow-sm sm:p-6" @disabled(! $state['form_editable'])>
                    <legend class="px-1 font-display text-xl font-semibold tracking-tight">Registration Folder</legend>
                    <p class="mt-3 text-sm leading-6 text-catalyst-ink/75">Upload all required registration documents to one Google Drive folder. Make sure the official Catalyst account has Viewer access before submitting your registration. Do not make the folder publicly accessible.</p>
                    <label class="mt-5 block text-sm font-medium">Google Drive Folder URL<input class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none disabled:bg-catalyst-grey/10" type="url" wire:model="form.drive_url">@error('form.drive_url') <span class="mt-1 block text-sm text-status-error">{{ $message }}</span> @enderror</label>
                </fieldset>

                <fieldset class="border border-catalyst-grey/30 bg-white p-5 shadow-sm sm:p-6" @disabled(! $state['form_editable'])>
                    <legend class="px-1 font-display text-xl font-semibold tracking-tight">Payment</legend>
                    @if ($state['payment_required'])
                        <div class="mt-5 grid gap-6 lg:grid-cols-[12rem_minmax(0,1fr)]">
                            <div class="grid min-h-48 place-items-center border border-dashed border-catalyst-grey/60 bg-catalyst-grey/10 p-4 text-center"><p class="text-sm font-medium">QRIS Payment Placeholder</p><p class="mt-2 text-xs leading-5 text-catalyst-grey">Replace with the approved QRIS asset.</p></div>
                            <div><p class="font-medium">Competition fee: {{ $state['competition']['fee'] }}</p><p class="mt-2 text-sm leading-6 text-catalyst-ink/75">After completing the payment, upload your payment proof to the same registration Google Drive folder above.</p><div class="mt-5 grid gap-4 sm:grid-cols-2"><label class="block text-sm">Sender Name<input class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none disabled:bg-catalyst-grey/10" type="text" wire:model="form.payment_sender"></label><label class="block text-sm">Payment Date<input class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none disabled:bg-catalyst-grey/10" type="date" wire:model="form.payment_date"></label><label class="block text-sm">Payment Time<input class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none disabled:bg-catalyst-grey/10" type="time" wire:model="form.payment_time"></label><label class="block text-sm">Optional Note<input class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none disabled:bg-catalyst-grey/10" type="text" wire:model="form.payment_note"></label></div></div>
                        </div>
                    @else
                        <div class="mt-5 border border-status-success/30 bg-status-success/10 p-4 text-sm leading-6 text-catalyst-ink/80"><p class="font-medium text-status-success">Payment waived</p><p class="mt-1">No payment confirmation is required for this registration.</p></div>
                    @endif
                </fieldset>

                <section class="border border-catalyst-grey/30 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="review-heading">
                    <h2 id="review-heading" class="font-display text-xl font-semibold tracking-tight">Review</h2>
                    <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                        <div><dt class="text-catalyst-grey">Competition</dt><dd class="mt-1 font-medium">{{ $state['competition']['name'] }}</dd></div>
                        <div><dt class="text-catalyst-grey">Team</dt><dd class="mt-1 font-medium">{{ $form['team_name'] ?: 'Not set' }}</dd></div>
                        <div><dt class="text-catalyst-grey">Institution</dt><dd class="mt-1 font-medium">{{ $form['institution'] ?: 'Not set' }}</dd></div>
                        <div><dt class="text-catalyst-grey">Members</dt><dd class="mt-1 font-medium">{{ collect($form['members'])->filter(fn ($member) => filled($member['legal_name']))->count() }} people</dd></div>
                        <div><dt class="text-catalyst-grey">Registration folder</dt><dd class="mt-1 font-medium">{{ $form['drive_url'] ? 'Provided' : 'Missing' }}</dd></div>
                        <div><dt class="text-catalyst-grey">Payment</dt><dd class="mt-1"><x-dashboard.status-pill :label="$state['payment']['label']" :tone="$state['payment']['tone']" /></dd></div>
                    </dl>
                </section>

                <section class="border border-catalyst-grey/30 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="whatsapp-heading">
                    <h2 id="whatsapp-heading" class="font-display text-xl font-semibold tracking-tight">WhatsApp Group Access</h2>
                    <p class="mt-3 text-sm leading-6 text-catalyst-ink/75">Stay updated with competition announcements and coordination.</p>
                    @if ($state['whatsapp']['state'] === 'available')
                        <a class="mt-4 inline-flex bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" href="{{ $state['whatsapp_url'] }}">Join WhatsApp Group</a>
                    @else
                        <p class="mt-4 text-sm text-catalyst-grey">Available immediately after you submit your registration.</p>
                    @endif
                </section>

                @if ($state['form_editable'])
                    <button class="w-full bg-linear-to-b from-catalyst-primary to-catalyst-green px-4 py-4 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="submit">Submit Registration</button>
                @endif
            </form>

            <details class="border border-catalyst-grey/30 bg-white p-4">
                <summary class="cursor-pointer text-sm font-medium text-catalyst-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary">Prototype state</summary>
                <div class="mt-4 max-w-xs"><label class="block text-sm text-catalyst-ink/80" for="registration-detail-scenario">Review scenario</label><select id="registration-detail-scenario" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-2 text-sm text-catalyst-ink focus:border-catalyst-primary focus:outline-none" wire:model.live="scenario">@foreach ($scenarios as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div>
            </details>
        </div>
    </x-ui.container>

    @if ($showConfirm)
        <div class="fixed inset-0 z-30 grid place-items-center bg-catalyst-ink/40 p-5" role="dialog" aria-modal="true" aria-labelledby="submit-registration-title" wire:keydown.escape="cancelConfirmation">
            <div class="w-full max-w-md border border-catalyst-grey/30 bg-white p-6 shadow-lg">
                <h2 id="submit-registration-title" class="font-display text-2xl font-semibold tracking-tight">Submit registration?</h2>
                <p class="mt-3 text-sm leading-6 text-catalyst-ink/75">After submitting, your registration will be sent to the Catalyst team for review. You will not be able to edit it unless a revision is requested.</p>
                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"><button class="border border-catalyst-grey/50 px-4 py-3 text-sm font-medium focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="cancelConfirmation">Cancel</button><button class="bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="submitRegistration">Submit Registration</button></div>
            </div>
        </div>
    @endif
</div>
