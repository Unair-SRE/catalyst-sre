<div class="space-y-6">
    <dl class="grid gap-4 sm:grid-cols-2">
        <div><dt class="text-sm text-gray-500">Team</dt><dd class="font-medium">{{ $payment->registration->team->name }}</dd></div>
        <div><dt class="text-sm text-gray-500">Captain</dt><dd class="font-medium">{{ $payment->registration->team->captain->name }}</dd></div>
        <div><dt class="text-sm text-gray-500">Competition</dt><dd class="font-medium">{{ $payment->registration->competition->name }}</dd></div>
        <div><dt class="text-sm text-gray-500">Sender</dt><dd class="font-medium">{{ $payment->sender_name }}</dd></div>
        <div><dt class="text-sm text-gray-500">Status</dt><dd class="font-medium">{{ $payment->status?->value ?? 'NOT_SUBMITTED' }}</dd></div>
        <div><dt class="text-sm text-gray-500">Verified by</dt><dd class="font-medium">{{ $payment->verifier?->name ?? '—' }}</dd></div>
    </dl>

    @if (str_starts_with($payment->payment_proof_file_id, 'seed-'))
        <p class="rounded-lg bg-warning-50 p-3 text-sm text-warning-700">Seeder stores payment metadata only. Upload a real proof to preview the ImageKit asset.</p>
    @endif

    <figure class="overflow-hidden rounded-xl border bg-gray-50 p-3">
        <img class="mx-auto max-h-[65vh] w-auto object-contain" src="{{ $proofUrl }}" alt="Payment proof from {{ $payment->sender_name }}">
        <figcaption class="mt-3 break-all text-xs text-gray-500">File ID: {{ $payment->payment_proof_file_id }}</figcaption>
    </figure>
</div>
