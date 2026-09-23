<div>
    <x-ui.container class="py-8 sm:py-10 lg:py-14">
        <div class="mx-auto max-w-4xl space-y-8">
            <header>
                <p class="text-xs font-medium uppercase tracking-widest text-catalyst-muted">Competition</p>
                <h1 class="mt-3 font-display text-3xl font-medium tracking-tight sm:text-4xl">Team Management</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-catalyst-ink/75">Create one team, upload every participant's KTM, and keep the data complete before registration.</p>
            </header>

            @if ($feedback)
                <p class="border border-status-success/30 bg-status-success/10 p-4 text-sm text-status-success-ink" role="status">{{ $feedback }}</p>
            @endif

            <form class="space-y-5 border border-catalyst-grey/30 bg-white p-5 sm:p-6" wire:submit="saveTeam">
                <div>
                    <h2 class="font-display text-xl font-semibold">Team details</h2>
                    @if ($team?->isLocked())
                        <p class="mt-2 text-sm text-status-warning-ink">Team data is locked because a payment proof has been submitted.</p>
                    @endif
                </div>
                <fieldset class="grid gap-5 sm:grid-cols-2" @disabled($team?->isLocked())>
                    <label class="block text-sm font-medium">Team name
                        <input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="text" wire:model="teamForm.name">
                        @error('teamForm.name') <span class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span> @enderror
                    </label>
                    <label class="block text-sm font-medium">Institution
                        <input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="text" wire:model="teamForm.institution">
                        @error('teamForm.institution') <span class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span> @enderror
                    </label>
                </fieldset>
                @if (! $team?->isLocked())
                    <button class="bg-catalyst-primary px-4 py-3 text-sm font-medium text-white" type="submit">{{ $team ? 'Save team' : 'Create team' }}</button>
                @endif
            </form>

            @if ($team)
                <section class="space-y-5 border border-catalyst-grey/30 bg-white p-5 sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div><h2 class="font-display text-xl font-semibold">Captain KTM</h2><p class="mt-1 text-sm text-catalyst-ink/70">{{ $team->captain->name }} · {{ $team->captain->email }}</p></div>
                        @if ($team->captain->hasCompleteKtm())
                            <a class="text-sm font-medium text-catalyst-primary underline underline-offset-4" href="{{ route('dashboard.private-files.captain-ktm', $team->captain) }}" target="_blank" rel="noopener">View current KTM</a>
                        @endif
                    </div>
                    @if (! $team->isLocked())
                        <form class="flex flex-col gap-4 sm:flex-row sm:items-end" wire:submit="uploadCaptainKtm">
                            <label class="block flex-1 text-sm font-medium">{{ $team->captain->hasCompleteKtm() ? 'Replace KTM' : 'Upload KTM' }}
                                <input class="mt-2 block w-full text-sm" type="file" wire:model="captainKtm" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                                <span class="mt-1 block text-xs text-catalyst-muted">JPG, JPEG, or PNG. Maximum 10 MB.</span>
                                @error('captainKtm') <span class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span> @enderror
                            </label>
                            <button class="bg-catalyst-primary px-4 py-3 text-sm font-medium text-white disabled:opacity-60" type="submit" wire:loading.attr="disabled">Upload</button>
                        </form>
                    @endif
                </section>

                <section class="space-y-6 border border-catalyst-grey/30 bg-white p-5 sm:p-6">
                    <div><h2 class="font-display text-xl font-semibold">Members</h2><p class="mt-1 text-sm text-catalyst-ink/70">Maximum two members besides the captain.</p></div>
                    <div class="space-y-4">
                        @forelse ($team->members as $member)
                            <article class="border border-catalyst-grey/30 p-4" wire:key="member-{{ $member->id }}">
                                @if ($editingMemberId === $member->id)
                                    <form class="space-y-4" wire:submit="updateMember">
                                        <div class="grid gap-4 sm:grid-cols-3">
                                            <label class="text-sm">Name<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" wire:model="editMemberForm.name">@error('editMemberForm.name') <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                                            <label class="text-sm">Email<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="email" wire:model="editMemberForm.email">@error('editMemberForm.email') <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                                            <label class="text-sm">WhatsApp<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" wire:model="editMemberForm.whatsapp">@error('editMemberForm.whatsapp') <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                                        </div>
                                        <label class="block text-sm">Replace KTM (optional)<input class="mt-2 block w-full" type="file" wire:model="editMemberKtm" accept=".jpg,.jpeg,.png,image/jpeg,image/png">@error('editMemberKtm') <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                                        <div class="flex gap-3"><button class="bg-catalyst-primary px-4 py-2 text-sm font-medium text-white" type="submit">Save member</button><button class="border border-catalyst-grey/50 px-4 py-2 text-sm" type="button" wire:click="cancelEditingMember">Cancel</button></div>
                                    </form>
                                @else
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                        <div><h3 class="font-medium">{{ $member->name }}</h3><p class="mt-1 text-sm text-catalyst-ink/70">{{ $member->email }} · {{ $member->whatsapp }}</p></div>
                                        <div class="flex flex-wrap gap-3">
                                            <a class="text-sm font-medium text-catalyst-primary underline underline-offset-4" href="{{ route('dashboard.private-files.team-member-ktm', $member) }}" target="_blank" rel="noopener">View KTM</a>
                                            @if (! $team->isLocked())
                                                <button class="text-sm font-medium" type="button" wire:click="startEditingMember({{ $member->id }})">Edit</button>
                                                <button class="text-sm font-medium text-status-error-ink" type="button" wire:click="removeMember({{ $member->id }})" wire:confirm="Remove this member and their KTM?">Remove</button>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </article>
                        @empty
                            <p class="text-sm text-catalyst-muted">No members added. A team may consist of the captain only.</p>
                        @endforelse
                    </div>

                    @if (! $team->isLocked() && $team->members->count() < 2)
                        <form class="space-y-4 border-t border-catalyst-grey/30 pt-5" wire:submit="addMember">
                            <h3 class="font-medium">Add member</h3>
                            <div class="grid gap-4 sm:grid-cols-3">
                                <label class="text-sm">Name<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" wire:model="memberForm.name">@error('memberForm.name') <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                                <label class="text-sm">Email<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="email" wire:model="memberForm.email">@error('memberForm.email') <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                                <label class="text-sm">WhatsApp<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" wire:model="memberForm.whatsapp">@error('memberForm.whatsapp') <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                            </div>
                            <label class="block text-sm font-medium">KTM<input class="mt-2 block w-full" type="file" wire:model="memberKtm" accept=".jpg,.jpeg,.png,image/jpeg,image/png"><span class="mt-1 block text-xs text-catalyst-muted">JPG, JPEG, or PNG. Maximum 10 MB.</span>@error('memberKtm') <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                            <button class="bg-catalyst-primary px-4 py-3 text-sm font-medium text-white disabled:opacity-60" type="submit" wire:loading.attr="disabled">Add member</button>
                        </form>
                    @endif
                </section>
            @endif
        </div>
    </x-ui.container>
</div>
