<div class="space-y-5">
    <dl class="grid gap-4 sm:grid-cols-2">
        <div><dt class="text-sm text-gray-500">Participant</dt><dd class="font-medium">{{ $personName }}</dd></div>
        <div><dt class="text-sm text-gray-500">Team</dt><dd class="font-medium">{{ $teamName }}</dd></div>
    </dl>

    @if (str_starts_with($fileId, 'seed-'))
        <p class="rounded-lg bg-warning-50 p-3 text-sm text-warning-700">Seeder stores KTM metadata only. Upload a real KTM to preview the ImageKit asset.</p>
    @endif

    <figure class="overflow-hidden rounded-xl border bg-gray-50 p-3">
        <img class="mx-auto max-h-[65vh] w-auto object-contain" src="{{ $imageUrl }}" alt="KTM {{ $personName }}">
        <figcaption class="mt-3 break-all text-xs text-gray-500">File ID: {{ $fileId }}</figcaption>
    </figure>
</div>
