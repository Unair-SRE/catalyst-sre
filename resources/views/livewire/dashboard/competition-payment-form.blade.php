<div>
    <x-ui.container class="py-8 sm:py-10 lg:py-14">
        <div class="mx-auto max-w-3xl space-y-8">
            <header><a class="text-sm text-catalyst-primary underline" href="{{ route('dashboard.registration.show', $competition) }}">Back to registration</a>
                <h1 class="mt-5 font-display text-3xl font-medium">Competition Payment</h1><p class="mt-3">{{ $payment->registration->competition->name }}</p>
            </header>
            @if ($feedback)<p class="border border-status-success/30 bg-status-success/5 p-4 text-sm" role="status">{{ $feedback }}</p>@endif
            @if ($errors->any())<div class="border border-status-error/30 p-4 text-sm text-status-error-ink" role="alert">@foreach ($errors->all() as $message)<p>{{ $message }}</p>@endforeach</div>@endif
            <section class="grid gap-4 border border-catalyst-grey/30 p-5 sm:grid-cols-3">
                <div><p class="text-xs text-catalyst-muted">Fee</p><p class="mt-2">IDR {{ number_format((float) $payment->registration->competition->registration_fee, 0, ',', '.') }}</p></div>
                <div><p class="text-xs text-catalyst-muted">Registration</p><p class="mt-2">{{ $payment->registration->status->value }}</p></div>
                <div><p class="text-xs text-catalyst-muted">Payment</p><p class="mt-2">{{ $payment->status?->value ?? 'NOT_SUBMITTED' }}</p></div>
            </section>
            <x-dashboard.drive-instructions />
            @if ($payment->registration->team->hasDocumentsFolder())
                <a class="inline-flex text-catalyst-primary underline" href="{{ route('dashboard.team.documents', $payment->registration->team) }}" target="_blank" rel="noopener noreferrer">Open team folder</a>
            @else
                <p class="text-sm">Add your team folder before confirming payment.</p><a class="text-catalyst-primary underline" href="{{ route('dashboard.team.index') }}">Manage team folder</a>
            @endif
            @if ($payment->review_note)<section class="border border-status-warning/30 bg-status-warning/5 p-5"><h2 class="font-semibold">Committee review note</h2><p class="mt-2 whitespace-pre-line text-sm">{{ $payment->review_note }}</p></section>@endif
            @if ($payment->status === null || $payment->status === \App\Enums\PaymentStatus::Rejected)
                <form class="space-y-5 border border-catalyst-grey/30 p-5 sm:p-6" wire:submit="submit">
                    <img class="mx-auto max-h-72 w-full object-contain" src="{{ asset(config('services.catalyst.qris_asset')) }}" alt="Catalyst payment QRIS">
                    <p class="text-sm">Add <strong>Bukti_Bayar_{{ strtoupper($competition) }}.jpg</strong> (or PDF/PNG) to the same team folder. @if ($payment->status === \App\Enums\PaymentStatus::Rejected)Correct the documents or sharing permissions mentioned by the committee before submitting again.@endif</p>
                    <label class="block text-sm font-medium">Sender name<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" wire:model="senderName" required maxlength="120"></label>
                    <label class="flex items-start gap-3 text-sm leading-6"><input class="mt-1" type="checkbox" wire:model="documentsConfirmed" required><span>I have added all KTM files and this competition's payment proof to the team folder and shared Viewer access with {{ config('services.catalyst.documents_reviewer_email') }}.</span></label>
                    <button class="bg-catalyst-primary px-4 py-3 text-sm text-white disabled:opacity-50" type="submit" wire:loading.attr="disabled" @disabled(! $payment->registration->team->hasDocumentsFolder()) wire:confirm="Submit these documents for review? Team details will be locked.">{{ $payment->status ? 'Resubmit corrected documents' : 'Payment proof added ? submit for review' }}</button>
                </form>
            @elseif ($payment->status === \App\Enums\PaymentStatus::Verified)
                <p class="border border-status-success/30 p-5 text-sm">Your registration and payment have been verified by the committee.</p>
            @else
                <p class="border border-catalyst-grey/30 p-5 text-sm">Your documents are waiting for committee review. No further confirmation is needed.</p>
            @endif
            @if ($setting?->contact_person_whatsapp)<a class="inline-flex text-sm text-catalyst-primary underline" href="https://wa.me/{{ preg_replace('/\D+/', '', $setting->contact_person_whatsapp) }}" target="_blank" rel="noopener noreferrer">Contact {{ $setting->contact_person_name ?: 'Catalyst' }}</a>@endif
        </div>
    </x-ui.container>
</div>
