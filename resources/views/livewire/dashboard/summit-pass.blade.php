<div>
    <x-ui.container class="py-8 sm:py-10 lg:py-12">
        <div class="space-y-8 lg:space-y-10">
            <header class="max-w-3xl">
                <p class="text-sm text-catalyst-grey">Summit experience</p>
                <h1 class="mt-5 font-display text-3xl font-semibold tracking-tight text-catalyst-black sm:text-4xl">Summit Pass</h1>
                <p class="mt-4 text-base leading-7 text-catalyst-ink/80 sm:text-lg">Your access to Catalyst Summit Talkshow and Exhibition.</p>
            </header>

            @if ($state['status'] === 'NO_PASS')
                <section class="max-w-3xl border border-catalyst-grey/30 bg-white p-6 shadow-sm sm:p-8" aria-labelledby="summit-empty-heading">
                    <svg class="size-9 text-catalyst-primary" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 7h16v10H4zM8 7V5.5A1.5 1.5 0 0 1 9.5 4h5A1.5 1.5 0 0 1 16 5.5V7m-5 5h2" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" /></svg>
                    <h2 id="summit-empty-heading" class="mt-5 font-display text-2xl font-semibold tracking-tight">Experience Catalyst Summit</h2>
                    <p class="mt-3 max-w-xl text-sm leading-6 text-catalyst-ink/75">Get one Summit Pass for access to the Talkshow and Exhibition on Summit Day.</p>

                    <dl class="mt-7 grid gap-5 border-catalyst-grey/30 border-y py-5 text-sm sm:grid-cols-3">
                        <div><dt class="text-catalyst-grey">Date</dt><dd class="mt-1 font-medium">{{ $state['event']['date_label'] }} · {{ $state['event']['time_label'] }}</dd></div>
                        <div><dt class="text-catalyst-grey">Venue</dt><dd class="mt-1 font-medium">{{ $state['event']['venue'] }}</dd></div>
                        <div><dt class="text-catalyst-grey">Ticket price</dt><dd class="mt-1 font-medium">{{ $state['event']['price'] }}</dd></div>
                    </dl>

                    <p class="mt-5 border border-status-info/30 bg-status-info/5 p-4 text-sm leading-6 text-catalyst-ink/80">Competition registration does not include a Summit Pass. Each account can purchase one non-transferable pass.</p>
                    <button class="mt-6 inline-flex min-h-11 items-center justify-center bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="startPurchase">Get Summit Pass</button>
                </section>
            @elseif ($state['status'] === 'REJECTED' && ! $form_editable)
                <section class="max-w-3xl border border-status-error/30 bg-status-error/5 p-6 sm:p-8" aria-labelledby="summit-rejected-heading">
                    <x-dashboard.status-pill :label="$state['status_display']['label']" :tone="$state['status_display']['tone']" />
                    <h2 id="summit-rejected-heading" class="mt-4 font-display text-2xl font-semibold tracking-tight">Payment needs an update</h2>
                    <p class="mt-3 text-sm leading-6 text-catalyst-ink/75">{{ $state['rejection_reason'] }}</p>
                    <button class="mt-6 inline-flex min-h-11 items-center justify-center bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="updatePayment">Update Payment</button>
                </section>
            @endif

            @if ($form_editable)
                <form class="max-w-4xl space-y-6" wire:submit="openConfirmation">
                    @if ($state['status'] === 'REJECTED')
                        <section class="border border-status-warning/30 bg-status-warning/5 p-5 sm:p-6" aria-labelledby="payment-update-heading">
                            <h2 id="payment-update-heading" class="font-display text-xl font-semibold tracking-tight">Update your payment</h2>
                            <p class="mt-2 text-sm leading-6 text-catalyst-ink/75">Previous review note: {{ $state['rejection_reason'] }}</p>
                        </section>
                    @endif

                    <fieldset class="border border-catalyst-grey/30 bg-white p-5 shadow-sm sm:p-6">
                        <legend class="px-1 font-display text-xl font-semibold tracking-tight">Attendee Information</legend>
                        <p class="mt-3 text-sm leading-6 text-catalyst-ink/75">This pass is linked to your account and cannot be transferred.</p>
                        <div class="mt-5 grid gap-5 sm:grid-cols-2">
                            <div>
                                <label class="block text-sm font-medium" for="summit-name">Full / Legal Name</label>
                                <input id="summit-name" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none" type="text" wire:model="attendee.name" @error('attendee.name') aria-invalid="true" aria-describedby="summit-name-error" @enderror>
                                @error('attendee.name') <p id="summit-name-error" class="mt-2 text-sm text-status-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium" for="summit-email">Email</label>
                                <input id="summit-email" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none" type="email" wire:model="attendee.email" @error('attendee.email') aria-invalid="true" aria-describedby="summit-email-error" @enderror>
                                @error('attendee.email') <p id="summit-email-error" class="mt-2 text-sm text-status-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium" for="summit-whatsapp">WhatsApp</label>
                                <input id="summit-whatsapp" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none" type="tel" wire:model="attendee.whatsapp" @error('attendee.whatsapp') aria-invalid="true" aria-describedby="summit-whatsapp-error" @enderror>
                                @error('attendee.whatsapp') <p id="summit-whatsapp-error" class="mt-2 text-sm text-status-error">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium" for="summit-institution">Institution</label>
                                <input id="summit-institution" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none" type="text" wire:model="attendee.institution" @error('attendee.institution') aria-invalid="true" aria-describedby="summit-institution-error" @enderror>
                                @error('attendee.institution') <p id="summit-institution-error" class="mt-2 text-sm text-status-error">{{ $message }}</p> @enderror
                            </div>
                        </div>
                    </fieldset>

                    <section class="border border-catalyst-grey/30 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="pass-information-heading">
                        <h2 id="pass-information-heading" class="font-display text-xl font-semibold tracking-tight">Summit Pass Information</h2>
                        <dl class="mt-5 grid gap-5 text-sm sm:grid-cols-2">
                            <div><dt class="text-catalyst-grey">Access</dt><dd class="mt-1 font-medium">{{ $state['event']['access'] }}</dd></div>
                            <div><dt class="text-catalyst-grey">Event date</dt><dd class="mt-1 font-medium">{{ $state['event']['date_label'] }} · {{ $state['event']['time_label'] }}</dd></div>
                            <div><dt class="text-catalyst-grey">Venue</dt><dd class="mt-1 font-medium">{{ $state['event']['venue'] }}</dd></div>
                            <div><dt class="text-catalyst-grey">Ticket price</dt><dd class="mt-1 font-medium">{{ $state['event']['price'] }}</dd></div>
                        </dl>
                        <p class="mt-5 border-catalyst-grey/30 border-t pt-5 text-sm leading-6 text-catalyst-ink/75">One ticket per account. The Summit Pass is assigned to the attendee above and is non-transferable. Corrections will require Catalyst admin support.</p>
                    </section>

                    <fieldset class="border border-catalyst-grey/30 bg-white p-5 shadow-sm sm:p-6">
                        <legend class="px-1 font-display text-xl font-semibold tracking-tight">Payment</legend>
                        <div class="mt-5 grid gap-6 lg:grid-cols-[13rem_minmax(0,1fr)]">
                            <div class="grid min-h-52 place-items-center border border-dashed border-catalyst-grey/60 bg-catalyst-grey/5 p-5 text-center" aria-label="QRIS Payment Placeholder">
                                <div><p class="text-sm font-medium">QRIS Payment Placeholder</p><p class="mt-2 text-xs leading-5 text-catalyst-grey">Replace with the approved QRIS asset.</p></div>
                            </div>
                            <div>
                                <p class="font-medium">Amount: {{ $state['event']['price'] }}</p>
                                <p class="mt-2 text-sm leading-6 text-catalyst-ink/75">Complete payment using the official QRIS asset, then provide the payment details and proof below for manual verification.</p>
                                <div class="mt-5 grid gap-4 sm:grid-cols-2">
                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium" for="summit-sender">Sender Name</label>
                                        <input id="summit-sender" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none" type="text" wire:model="payment.sender_name" @error('payment.sender_name') aria-invalid="true" aria-describedby="summit-sender-error" @enderror>
                                        @error('payment.sender_name') <p id="summit-sender-error" class="mt-2 text-sm text-status-error">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium" for="summit-payment-date">Payment Date</label>
                                        <input id="summit-payment-date" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none" type="date" wire:model="payment.date" @error('payment.date') aria-invalid="true" aria-describedby="summit-payment-date-error" @enderror>
                                        @error('payment.date') <p id="summit-payment-date-error" class="mt-2 text-sm text-status-error">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium" for="summit-payment-time">Payment Time</label>
                                        <input id="summit-payment-time" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-3 text-sm focus:border-catalyst-primary focus:outline-none" type="time" wire:model="payment.time" @error('payment.time') aria-invalid="true" aria-describedby="summit-payment-time-error" @enderror>
                                        @error('payment.time') <p id="summit-payment-time-error" class="mt-2 text-sm text-status-error">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="mt-5">
                                    <label class="block text-sm font-medium" for="summit-payment-proof">Payment Proof</label>
                                    <input id="summit-payment-proof" class="mt-2 block w-full text-sm text-catalyst-ink file:mr-4 file:min-h-11 file:border-0 file:bg-catalyst-primary file:px-4 file:py-3 file:text-sm file:font-medium file:text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="file" accept=".pdf,.png,.jpg,.jpeg,application/pdf,image/png,image/jpeg" x-on:change="const file = $event.target.files[0]; if (file) $wire.selectProof({ name: file.name, type: file.type, size_bytes: file.size })">
                                    <p class="mt-2 text-xs leading-5 text-catalyst-grey">Prototype stores file metadata only; no proof file is uploaded.</p>
                                    @error('payment.proof') <p class="mt-2 text-sm text-status-error">Select payment proof before submitting.</p> @enderror
                                    @if ($state['payment']['proof'])
                                        <div class="mt-3 flex flex-col gap-3 border border-catalyst-grey/30 p-3 text-sm sm:flex-row sm:items-center sm:justify-between"><p class="min-w-0 truncate"><span class="font-medium">{{ $state['payment']['proof']['name'] }}</span> · {{ $state['payment']['proof']['size_label'] }}</p><button class="w-fit text-sm font-medium text-catalyst-primary underline underline-offset-4 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="removeProof">Remove</button></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </fieldset>

                    <section class="border border-catalyst-grey/30 bg-white p-5 shadow-sm sm:p-6" aria-labelledby="summit-review-heading">
                        <h2 id="summit-review-heading" class="font-display text-xl font-semibold tracking-tight">Review</h2>
                        <dl class="mt-5 grid gap-4 text-sm sm:grid-cols-2">
                            <div><dt class="text-catalyst-grey">Attendee</dt><dd class="mt-1 font-medium">{{ $state['attendee']['name'] ?: 'Not set' }}</dd></div>
                            <div><dt class="text-catalyst-grey">Access</dt><dd class="mt-1 font-medium">{{ $state['event']['access'] }}</dd></div>
                            <div><dt class="text-catalyst-grey">Ticket price</dt><dd class="mt-1 font-medium">{{ $state['event']['price'] }}</dd></div>
                            <div><dt class="text-catalyst-grey">Payment proof</dt><dd class="mt-1 font-medium">{{ $state['payment']['proof'] ? 'Selected' : 'Missing' }}</dd></div>
                        </dl>
                    </section>

                    <button class="inline-flex min-h-12 w-full items-center justify-center bg-linear-to-b from-catalyst-primary to-catalyst-green px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="submit">{{ $state['status'] === 'REJECTED' ? 'Resubmit Purchase' : 'Submit Purchase' }}</button>
                </form>
            @endif

            @if ($state['status'] === 'WAITING_VERIFICATION')
                <section class="max-w-4xl border border-status-info/30 bg-status-info/5 p-6 sm:p-8" aria-labelledby="payment-waiting-heading">
                    <x-dashboard.status-pill :label="$state['status_display']['label']" :tone="$state['status_display']['tone']" />
                    <h2 id="payment-waiting-heading" class="mt-4 font-display text-2xl font-semibold tracking-tight">Payment under review</h2>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-catalyst-ink/75">Your Summit Pass payment has been submitted and is being reviewed by the Catalyst team.</p>
                    <dl class="mt-6 grid gap-5 border-catalyst-grey/30 border-t pt-5 text-sm sm:grid-cols-2 lg:grid-cols-4">
                        <div><dt class="text-catalyst-grey">Attendee</dt><dd class="mt-1 font-medium">{{ $state['attendee']['name'] }}</dd></div>
                        <div><dt class="text-catalyst-grey">Ticket price</dt><dd class="mt-1 font-medium">{{ $state['event']['price'] }}</dd></div>
                        <div><dt class="text-catalyst-grey">Sender</dt><dd class="mt-1 font-medium">{{ $state['payment']['sender_name'] }}</dd></div>
                        <div><dt class="text-catalyst-grey">Submitted</dt><dd class="mt-1 font-medium">{{ $state['submitted_label'] }}</dd></div>
                    </dl>
                    <p class="mt-5 text-sm text-catalyst-ink/70">The active ticket and ticket QR will appear only after payment verification.</p>
                </section>
            @endif

            @if (in_array($state['status'], ['VERIFIED', 'CHECKED_IN'], true))
                <section class="max-w-4xl" aria-labelledby="active-ticket-heading">
                    <div class="mb-5">
                        <x-dashboard.status-pill :label="$state['status_display']['label']" :tone="$state['status_display']['tone']" />
                        <h2 id="active-ticket-heading" class="mt-4 font-display text-2xl font-semibold tracking-tight">{{ $state['status'] === 'CHECKED_IN' ? 'You’re checked in for Catalyst Summit' : 'Your Summit Pass is ready' }}</h2>
                        @if ($state['status'] === 'CHECKED_IN')
                            <p class="mt-2 text-sm leading-6 text-catalyst-ink/75">Checked in <time datetime="{{ $state['checked_in_at'] }}">{{ $state['checked_in_label'] }}</time>.</p>
                        @else
                            <p class="mt-2 text-sm leading-6 text-catalyst-ink/75">Check-in status: Not checked in yet. Check-in is completed by Catalyst staff.</p>
                        @endif
                    </div>

                    <article class="overflow-hidden bg-linear-to-br from-catalyst-ink via-catalyst-blue to-catalyst-primary p-6 text-white shadow-sm sm:p-8">
                        <div class="grid gap-8 md:grid-cols-[minmax(0,1fr)_13rem] md:items-center">
                            <div>
                                <p class="text-sm text-white/75">Catalyst 2026</p>
                                <h3 class="mt-3 font-display text-3xl font-semibold tracking-tight">{{ $state['event']['name'] }}</h3>
                                <p class="mt-2 text-base text-white/85">{{ $state['event']['access'] }}</p>
                                <dl class="mt-7 grid gap-4 text-sm sm:grid-cols-2">
                                    <div><dt class="text-white/65">Attendee</dt><dd class="mt-1 font-medium">{{ $state['attendee']['name'] }}</dd></div>
                                    <div><dt class="text-white/65">Ticket ID</dt><dd class="mt-1 font-medium tracking-wide">{{ $state['ticket']['id'] }}</dd></div>
                                    <div><dt class="text-white/65">Date</dt><dd class="mt-1 font-medium">{{ $state['event']['date_label'] }} · {{ $state['event']['time_label'] }}</dd></div>
                                    <div><dt class="text-white/65">Venue</dt><dd class="mt-1 font-medium">{{ $state['event']['venue'] }}</dd></div>
                                </dl>
                                <p class="mt-7 max-w-xl text-sm leading-6 text-white/75">This pass is assigned to your account and is non-transferable. Do not share the future production ticket code.</p>
                            </div>
                            <div class="grid aspect-square w-full max-w-52 place-items-center justify-self-center border border-dashed border-white/50 bg-white p-5 text-center text-catalyst-ink md:justify-self-end" role="img" aria-label="{{ $state['ticket']['qr_label'] }}">
                                <div><svg class="mx-auto size-8" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 4h6v6H4zm10 0h6v6h-6zM4 14h6v6H4zm11 0h2v2h-2zm3 0h2v5h-2zm-3 4h2v2h-2z" stroke="currentColor" stroke-width="1.5" /></svg><p class="mt-3 text-sm font-medium">Prototype QR</p><p class="mt-1 text-xs leading-5 text-catalyst-grey">Ticket QR Placeholder</p></div>
                            </div>
                        </div>
                    </article>
                </section>
            @endif

            @if ($feedback)
                <p class="max-w-4xl border border-status-success/30 bg-status-success/5 p-4 text-sm leading-6 text-catalyst-ink" role="status">{{ $feedback }}</p>
            @endif

            @if ($state['history'] !== [] && in_array($state['status'], ['WAITING_VERIFICATION', 'REJECTED'], true))
                <details class="max-w-4xl border border-catalyst-grey/30 bg-white p-4">
                    <summary class="cursor-pointer text-sm font-medium text-catalyst-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary">Payment status history</summary>
                    <ol class="mt-5 space-y-4 border-catalyst-grey/30 border-l pl-5">
                        @foreach ($state['history'] as $event)
                            <li class="relative"><span class="absolute -left-[1.7rem] top-1.5 size-2.5 rounded-full bg-catalyst-primary" aria-hidden="true"></span><p class="text-sm font-medium">{{ $event['event'] }}</p><p class="mt-1 text-sm text-catalyst-ink/75">{{ $event['detail'] }}</p><time class="mt-1 block text-xs text-catalyst-grey" datetime="{{ $event['timestamp'] }}">{{ $event['timestamp_label'] }}</time></li>
                        @endforeach
                    </ol>
                </details>
            @endif

            <details class="max-w-4xl border border-catalyst-grey/30 bg-white p-4">
                <summary class="cursor-pointer text-sm font-medium text-catalyst-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary">Prototype state</summary>
                <div class="mt-4 max-w-xs"><label class="block text-sm text-catalyst-ink/80" for="summit-pass-scenario">Review scenario</label><select id="summit-pass-scenario" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-2 text-sm text-catalyst-ink focus:border-catalyst-primary focus:outline-none" wire:model.live="scenario">@foreach ($scenarios as $value => $label)<option value="{{ $value }}">{{ $label }}</option>@endforeach</select></div>
            </details>
        </div>
    </x-ui.container>

    @if ($showConfirm)
        <div class="fixed inset-0 z-30 grid place-items-center bg-catalyst-ink/40 p-5" role="dialog" aria-modal="true" aria-labelledby="submit-summit-purchase-title" wire:keydown.escape="cancelConfirmation">
            <div class="w-full max-w-md border border-catalyst-grey/30 bg-white p-6 shadow-lg">
                <h2 id="submit-summit-purchase-title" class="font-display text-2xl font-semibold tracking-tight">Submit Summit Pass purchase?</h2>
                <p class="mt-3 text-sm leading-6 text-catalyst-ink/75">Your payment will be reviewed by the Catalyst team before your ticket becomes active.</p>
                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"><button class="border border-catalyst-grey/50 px-4 py-3 text-sm font-medium focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="cancelConfirmation">Cancel</button><button class="bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="submitPurchase">Submit Purchase</button></div>
            </div>
        </div>
    @endif
</div>
