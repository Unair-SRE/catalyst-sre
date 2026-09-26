<div>
    <x-ui.container class="py-8 sm:py-10 lg:py-14">
        <div class="mx-auto max-w-4xl space-y-8">
            <header>
                <p class="text-sm text-catalyst-muted">Competition</p>
                <h1 class="mt-3 font-display text-3xl font-medium sm:text-4xl">Team Management</h1>
                <p class="mt-3 text-sm leading-6">Create one team with up to three people, including the captain. Use one Google Drive folder for all KTM files and competition payment proofs.</p>
            </header>
            @if ($feedback)<p class="border border-status-success/30 bg-status-success/5 p-4 text-sm" role="status">{{ $feedback }}</p>@endif
            @if ($errors->any())<div class="border border-status-error/30 p-4 text-sm text-status-error-ink" role="alert"><ul>@foreach ($errors->all() as $message)<li>{{ $message }}</li>@endforeach</ul></div>@endif
            <section class="border border-catalyst-grey/30 p-5 sm:p-6">
                <h2 class="font-display text-xl font-semibold">Captain</h2>
                <p class="mt-3">{{ auth()->user()->name }}</p>
                <p class="text-sm text-catalyst-muted">{{ auth()->user()->email }} ? {{ auth()->user()->whatsapp }}</p>
                <a class="mt-3 inline-block text-sm text-catalyst-primary underline" href="{{ route('dashboard.profile.index') }}">Update captain profile</a>
            </section>
            <form wire:submit="{{ $team ? 'saveTeam' : 'createTeam' }}" class="space-y-6 border border-catalyst-grey/30 bg-white p-5 sm:p-6">
                <h2 class="font-display text-xl font-semibold">Team details</h2>
                @if ($team?->isLocked())<p class="text-sm text-status-warning-ink">Team data is locked after payment confirmation. Contact the committee for roster or folder-link corrections.</p>@endif
                <fieldset class="grid gap-5 sm:grid-cols-2" @disabled($team?->isLocked())>
                    @foreach (['name' => 'Team name', 'institution' => 'Institution'] as $key => $label)
                        <label class="text-sm font-medium">{{ $label }}<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" wire:model="teamForm.{{ $key }}" required maxlength="{{ $key === 'name' ? 120 : 160 }}"></label>
                    @endforeach
                </fieldset>
                @if (! $team)
                    <x-dashboard.drive-instructions />
                    <label class="block text-sm font-medium">Team Google Drive folder
                        <input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="url" wire:model="documentsDriveUrl" placeholder="https://drive.google.com/drive/folders/..." required maxlength="2048">
                    </label>
                    <div class="flex items-center justify-between gap-4"><h2 class="font-display text-xl font-semibold">Members (optional)</h2>
                        @if (count($setupMembers) < 2)<button class="border border-catalyst-primary px-4 py-2 text-sm" type="button" wire:click="addSetupMember">+ Add member</button>@endif
                    </div>
                    @foreach ($setupMembers as $index => $member)
                        <div class="space-y-4 border border-catalyst-grey/30 p-4" wire:key="setup-member-{{ $index }}">
                            <div class="flex justify-between"><h3>Member {{ $index + 1 }}</h3><button type="button" class="text-sm text-status-error-ink" wire:click="removeSetupMember({{ $index }})">Remove</button></div>
                            <x-dashboard.member-fields :prefix="'setupMembers.'.$index" />
                        </div>
                    @endforeach
                @endif
                @if (! $team?->isLocked())<button type="submit" class="bg-catalyst-primary px-5 py-3 text-sm text-white disabled:opacity-50" wire:loading.attr="disabled">{{ $team ? 'Save team details' : 'Create team' }}</button>@endif
            </form>
            @if ($team)
                <section class="space-y-5 border border-catalyst-grey/30 p-5 sm:p-6">
                    <h2 class="font-display text-xl font-semibold">Team documents</h2>
                    <x-dashboard.drive-instructions />
                    @if ($team->hasDocumentsFolder())
                        <a class="inline-flex text-catalyst-primary underline" href="{{ route('dashboard.team.documents', $team) }}" target="_blank" rel="noopener noreferrer">Open team folder</a>
                        <p class="text-sm text-catalyst-muted">Folder link saved. This does not mean the documents have been verified.</p>
                    @endif
                    @if (! $team->isLocked() || ! $team->documents_drive_url)
                        <form wire:submit="saveFolder" class="space-y-4">
                            <label class="block text-sm font-medium">Google Drive folder link<input class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="url" wire:model="documentsDriveUrl" required maxlength="2048" placeholder="https://drive.google.com/drive/folders/..."></label>
                            <button class="bg-catalyst-primary px-4 py-3 text-sm text-white" wire:loading.attr="disabled">Save folder</button>
                        </form>
                    @else
                        <p class="text-sm text-catalyst-muted">You can correct files and sharing permissions inside the same folder. Contact the committee if the folder link itself must change.</p>
                    @endif
                </section>
                <section class="space-y-5 border border-catalyst-grey/30 p-5 sm:p-6">
                    <h2 class="font-display text-xl font-semibold">Members ({{ $team->members->count() }} of 2)</h2>
                    @foreach ($team->members as $member)
                        <article class="space-y-4 border-b border-catalyst-grey/30 pb-5" wire:key="member-{{ $member->id }}">
                            @if ($editingMemberId === $member->id && ! $team->isLocked())
                                <form wire:submit="updateMember" class="space-y-4"><x-dashboard.member-fields prefix="editMemberForm" /><button class="bg-catalyst-primary px-4 py-2 text-white">Save member</button><button type="button" class="ml-3 text-sm" wire:click="cancelEditingMember">Cancel</button></form>
                            @else
                                <p class="font-semibold">{{ $member->name }}</p><p class="text-sm text-catalyst-muted">{{ $member->email }} ? {{ $member->whatsapp }}</p>
                                @if (! $team->isLocked())<div class="flex gap-4"><button class="text-sm text-catalyst-primary" type="button" wire:click="startEditingMember({{ $member->id }})">Edit</button><button class="text-sm text-status-error-ink" type="button" wire:click="removeMember({{ $member->id }})" wire:confirm="Remove this member?">Remove</button></div>@endif
                            @endif
                        </article>
                    @endforeach
                    @if (! $team->isLocked() && $team->members->count() < 2)
                        <form class="space-y-4" wire:submit="addMember"><x-dashboard.member-fields prefix="memberForm" /><p class="text-sm text-catalyst-muted">Add this member's KTM to the same team folder.</p><button class="bg-catalyst-primary px-4 py-3 text-sm text-white" wire:loading.attr="disabled">Add member</button></form>
                    @endif
                </section>
            @endif
        </div>
    </x-ui.container>
</div>
