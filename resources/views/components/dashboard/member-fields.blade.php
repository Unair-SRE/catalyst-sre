@props(['prefix'])
<div class="grid gap-4 sm:grid-cols-3">
    @foreach (['name' => 'Legal name', 'email' => 'Email', 'whatsapp' => 'WhatsApp'] as $key => $label)
        <label class="block text-sm font-medium">{{ $label }}
            <input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="{{ $key === 'email' ? 'email' : 'text' }}" wire:model="{{ $prefix }}.{{ $key }}" required maxlength="{{ $key === 'name' ? 120 : ($key === 'email' ? 255 : 20) }}">
            @error("$prefix.$key") <span class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span> @enderror
        </label>
    @endforeach
</div>
