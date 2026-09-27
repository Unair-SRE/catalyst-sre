<div class="space-y-5">
    <dl class="grid gap-4 sm:grid-cols-2">
        <div><dt>Team</dt><dd class="font-semibold">{{ $payment->registration->team->name }}</dd></div>
        <div><dt>Competition</dt><dd>{{ $payment->registration->competition->name }}</dd></div>
        <div><dt>Sender</dt><dd>{{ $payment->sender_name ?? 'Not submitted' }}</dd></div>
        <div><dt>Status</dt><dd>{{ $payment->status?->value ?? 'NOT_SUBMITTED' }}</dd></div>
        <div><dt>Expected fee</dt><dd>IDR {{ number_format((float) $payment->registration->competition->registration_fee, 0, ',', '.') }}</dd></div>
        <div><dt>Reviewed by</dt><dd>{{ $payment->verifier?->name ?? 'Not reviewed' }}</dd></div>
    </dl>
    <p>Check KTM files for these participants:</p>
    <ul class="list-disc pl-5"><li>{{ $payment->registration->team->captain->name }} (Captain)</li>@foreach ($payment->registration->team->members as $member)<li>{{ $member->name }}</li>@endforeach</ul>
    <p>Check <strong>Bukti_Bayar_{{ $payment->registration->competition->code->value }}</strong> in the folder against the expected fee.</p>
    @if ($payment->registration->team->hasDocumentsFolder())
        <a class="font-semibold underline" href="{{ $payment->registration->team->documents_drive_url }}" target="_blank" rel="noopener noreferrer">Open team Google Drive folder</a>
        <p class="text-sm">The folder must use <strong>Anyone with the link</strong> access with the <strong>Viewer</strong> role. A saved link does not prove the documents are accessible or valid.</p>
    @else
        <p>No team folder has been provided. Ask the captain to add it from Team Management.</p>
    @endif
    @if ($payment->review_note)<p class="whitespace-pre-line">{{ $payment->review_note }}</p>@endif
</div>
