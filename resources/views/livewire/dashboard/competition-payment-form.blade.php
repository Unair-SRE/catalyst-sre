<div>
    <x-ui.container class="py-8 sm:py-10 lg:py-14">
        <div class="mx-auto max-w-3xl space-y-8">
            <header>
                <a class="text-sm font-medium text-catalyst-primary underline underline-offset-4" href="{{ route('dashboard.registration.show', $competition) }}">Back to registration</a>
                <p class="mt-6 text-sm font-medium tracking-widest text-catalyst-muted">{{ $payment->registration->competition->code->value }}</p>
                <h1 class="mt-2 font-display text-3xl font-medium tracking-tight">Competition Payment</h1>
                <p class="mt-3 text-catalyst-ink/75">{{ $payment->registration->competition->name }}</p>
            </header>

            @if ($feedback)
                <p class="border border-status-success/30 bg-status-success/10 p-4 text-sm text-status-success-ink" role="status">{{ $feedback }}</p>
            @endif

            <section class="border border-catalyst-grey/30 bg-white p-5 sm:p-6">
                <dl class="grid gap-4 text-sm sm:grid-cols-2">
                    <div><dt class="text-catalyst-muted">Registration</dt><dd class="mt-1 font-medium">{{ $payment->registration->status->value }}</dd></div>
                    <div><dt class="text-catalyst-muted">Payment</dt><dd class="mt-1 font-medium">{{ $payment->status?->value ?? 'NOT_SUBMITTED' }}</dd></div>
                    <div><dt class="text-catalyst-muted">Fee</dt><dd class="mt-1 font-medium">IDR {{ number_format((float) $payment->registration->competition->registration_fee, 0, ',', '.') }}</dd></div>
                </dl>
            </section>

            @if ($payment->status?->value === 'REJECTED')
                <section class="border border-status-error/30 bg-status-error/5 p-5">
                    <h2 class="font-display text-xl font-semibold">Payment rejected</h2>
                    <p class="mt-2 text-sm leading-6">Corrections are handled through the Catalyst contact person. Re-upload is not available on this website.</p>
                    @if ($setting?->contact_person_whatsapp)
                        <a class="mt-4 inline-flex bg-catalyst-primary px-4 py-3 text-sm font-medium text-white" href="https://wa.me/{{ preg_replace('/\D+/', '', $setting->contact_person_whatsapp) }}">Contact {{ $setting->contact_person_name ?: 'Catalyst' }}</a>
                    @endif
                </section>
            @elseif ($payment->status !== null)
                <section class="border border-catalyst-grey/30 bg-white p-5">
                    <h2 class="font-display text-xl font-semibold">Proof received</h2>
                    <p class="mt-2 text-sm leading-6 text-catalyst-ink/75">Your proof has been submitted. Participants cannot replace it from the website.</p>
                    <a class="mt-4 inline-flex text-sm font-medium text-catalyst-primary underline underline-offset-4" href="{{ route('dashboard.competition-payment.proof', $payment) }}" target="_blank" rel="noopener">View submitted proof</a>
                </section>
            @else
                <form class="space-y-6 border border-catalyst-grey/30 bg-white p-5 sm:p-6" wire:submit="submit">
                    <div class="grid gap-6 sm:grid-cols-[12rem_minmax(0,1fr)]">
                        <div class="min-h-48 overflow-hidden border border-catalyst-grey/30 bg-catalyst-grey/5">
                            <img class="h-full w-full object-contain" src="{{ asset(config('services.catalyst.qris_asset')) }}" alt="Catalyst payment QRIS">
                        </div>
                        <div class="space-y-5">
                            <label class="block text-sm font-medium">Sender Name
                                <input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="text" wire:model="senderName">
                                @error('senderName') <span class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span> @enderror
                            </label>
                            <label class="block text-sm font-medium">Payment Proof
                                <input class="mt-2 block w-full text-sm" type="file" wire:model="proof" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                                <span class="mt-2 block text-xs text-catalyst-muted">JPG, JPEG, or PNG. Maximum 2 MB.</span>
                                <span class="mt-2 block text-xs text-catalyst-primary" wire:loading wire:target="proof">Preparing preview...</span>
                                @error('proof') <span class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span> @enderror
                            </label>
                            @if ($proof)<div class="border border-catalyst-grey/30 bg-catalyst-neutral p-3"><img class="max-h-48 w-full object-contain" src="{{ $proof->temporaryUrl() }}" alt="Selected payment proof preview"><p class="mt-2 truncate text-xs text-catalyst-muted">{{ $proof->getClientOriginalName() }}</p></div>@endif
                        </div>
                    </div>
                    <button class="bg-catalyst-primary px-4 py-3 text-sm font-medium text-white disabled:opacity-60" type="submit" wire:loading.attr="disabled" wire:confirm="Submit this payment proof? Team data will be locked after upload."><span wire:loading.remove wire:target="submit">Submit Payment Proof</span><span wire:loading wire:target="submit">Uploading...</span></button>
                </form>
            @endif
        </div>
    </x-ui.container>
</div>
