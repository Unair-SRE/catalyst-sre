<div>
    <x-ui.container class="py-8 sm:py-10 lg:py-14">
        <div class="mx-auto max-w-4xl space-y-8">
            <header class="dashboard-page-heading">
                <p class="text-catalyst-muted">Competition</p>
                <h1 class="mt-3 font-display text-3xl font-medium tracking-tight sm:text-4xl">Team Management</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-catalyst-ink/75">
                    Create one team, add up to two members, and attach a valid KTM for every participant before registration.
                </p>
            </header>

            @if ($feedback)
                <p class="border border-status-success/30 bg-status-success/10 p-4 text-sm text-status-success-ink" role="status">
                    {{ $feedback }}
                </p>
            @endif

            <section class="grid border-y border-catalyst-grey/30 sm:grid-cols-3" aria-label="Team setup progress">
                <div class="py-4 sm:pr-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-catalyst-muted">01 · Team</p>
                    <p class="mt-2 text-sm font-semibold">{{ $team ? 'Created' : 'Not created' }}</p>
                </div>
                <div class="border-t border-catalyst-grey/20 py-4 sm:border-l sm:border-t-0 sm:px-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-catalyst-muted">02 · Captain KTM</p>
                    <p class="mt-2 text-sm font-semibold">{{ ($team?->captain ?? auth()->user())->hasCompleteKtm() || $captainKtm ? 'Ready' : 'Required' }}</p>
                </div>
                <div class="border-t border-catalyst-grey/20 py-4 sm:border-l sm:border-t-0 sm:pl-5">
                    <p class="text-xs font-medium uppercase tracking-wider text-catalyst-muted">03 · Members</p>
                    <p class="mt-2 text-sm font-semibold">{{ $team?->members->count() ?? count($setupMembers) }} of 2 added</p>
                </div>
            </section>

            @if (! $team)
                <form class="border border-catalyst-grey/30 bg-white" wire:submit="createTeam">
                    @error('setup')
                        <p class="m-5 border border-status-error/30 bg-status-error/5 p-4 text-sm text-status-error-ink sm:m-6" role="alert">{{ $message }}</p>
                    @enderror

                    <section class="space-y-5 p-5 sm:p-6" aria-labelledby="new-team-details-heading">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-catalyst-primary">01 · Team details</p>
                            <h2 id="new-team-details-heading" class="mt-2 font-display text-xl font-semibold">Tell us about your team</h2>
                            <p class="mt-2 text-sm leading-6 text-catalyst-ink/70">These details will be used for every competition registration.</p>
                        </div>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <label class="block text-sm font-medium" for="team-name">
                                Team name
                                <input id="team-name" class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="text" wire:model="teamForm.name" @error('teamForm.name') aria-invalid="true" aria-describedby="team-name-error" @enderror>
                                @error('teamForm.name') <span id="team-name-error" class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span> @enderror
                            </label>
                            <label class="block text-sm font-medium" for="team-institution">
                                Institution
                                <input id="team-institution" class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="text" wire:model="teamForm.institution" @error('teamForm.institution') aria-invalid="true" aria-describedby="team-institution-error" @enderror>
                                @error('teamForm.institution') <span id="team-institution-error" class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span> @enderror
                            </label>
                        </div>
                    </section>

                    <section class="space-y-5 border-t border-catalyst-grey/30 p-5 sm:p-6" aria-labelledby="new-captain-heading">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-catalyst-primary">02 · Captain</p>
                            <h2 id="new-captain-heading" class="mt-2 font-display text-xl font-semibold">Confirm the captain</h2>
                            <p class="mt-2 text-sm text-catalyst-ink/70">{{ auth()->user()->name }} · {{ auth()->user()->email }}</p>
                        </div>

                        @if (auth()->user()->hasCompleteKtm())
                            <div class="flex flex-col gap-3 border border-status-success/30 bg-status-success/5 p-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-semibold text-status-success-ink">Captain KTM is ready</p>
                                    <p class="mt-1 text-xs text-catalyst-muted">The KTM already saved on your profile will be used.</p>
                                </div>
                                <a class="text-sm font-medium text-catalyst-primary underline underline-offset-4" href="{{ route('dashboard.private-files.captain-ktm', auth()->user()) }}" target="_blank" rel="noopener">View current KTM</a>
                            </div>
                        @endif

                        <div>
                            <label class="flex min-h-32 cursor-pointer flex-col items-center justify-center border border-dashed px-5 py-6 text-center transition-colors hover:border-catalyst-primary hover:bg-catalyst-surface/40 {{ $errors->has('captainKtm') ? 'border-status-error-ink bg-status-error/5' : 'border-catalyst-grey/60 bg-catalyst-neutral/60' }}" for="captain-ktm">
                                <svg class="h-7 w-7 text-catalyst-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                <span class="mt-3 text-sm font-semibold text-catalyst-ink">{{ auth()->user()->hasCompleteKtm() ? 'Choose a replacement KTM (optional)' : 'Choose the captain KTM' }}</span>
                                <span class="mt-1 text-xs leading-5 text-catalyst-muted">JPG, JPEG, or PNG · maximum 2 MB</span>
                                <input id="captain-ktm" class="sr-only" type="file" wire:model="captainKtm" accept=".jpg,.jpeg,.png,image/jpeg,image/png" @error('captainKtm') aria-invalid="true" aria-describedby="captain-ktm-error" @enderror>
                            </label>
                            <p class="mt-2 text-xs font-medium text-catalyst-primary" wire:loading wire:target="captainKtm" role="status">Checking the selected file…</p>
                            @error('captainKtm') <p id="captain-ktm-error" class="mt-2 text-sm text-status-error-ink" role="alert">{{ $message }}</p> @enderror
                        </div>

                        @if ($captainKtm && ! $errors->has('captainKtm'))
                            <div class="grid gap-4 border border-catalyst-grey/30 p-4 sm:grid-cols-[7rem_1fr] sm:items-center">
                                <img class="h-28 w-full bg-catalyst-neutral object-contain" src="{{ $captainKtm->temporaryUrl() }}" alt="Selected captain KTM preview">
                                <div class="min-w-0"><p class="truncate text-sm font-semibold">{{ $captainKtm->getClientOriginalName() }}</p><p class="mt-1 text-xs text-catalyst-muted">{{ number_format($captainKtm->getSize() / 1024) }} KB · ready to create</p></div>
                            </div>
                        @endif
                    </section>

                    <section class="space-y-5 border-t border-catalyst-grey/30 p-5 sm:p-6" aria-labelledby="new-members-heading">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div>
                                <p class="text-xs font-medium uppercase tracking-wider text-catalyst-primary">03 · Members</p>
                                <h2 id="new-members-heading" class="mt-2 font-display text-xl font-semibold">Add team members</h2>
                                <p class="mt-2 text-sm leading-6 text-catalyst-ink/70">Optional. The captain already counts as the first participant.</p>
                            </div>
                            @if (count($setupMembers) < 2)
                                <button class="shrink-0 border border-catalyst-primary px-4 py-3 text-sm font-medium text-catalyst-primary" type="button" wire:click="addSetupMember">+ Add member</button>
                            @endif
                        </div>

                        @forelse ($setupMembers as $index => $setupMember)
                            <article class="space-y-4 border border-catalyst-grey/30 p-4 sm:p-5" wire:key="setup-member-{{ $index }}">
                                <div class="flex items-center justify-between gap-4">
                                    <h3 class="font-display text-lg font-semibold">Member {{ $index + 1 }}</h3>
                                    <button class="text-sm font-medium text-status-error-ink" type="button" wire:click="removeSetupMember({{ $index }})">Remove</button>
                                </div>
                                <div class="grid gap-4 sm:grid-cols-3">
                                    <label class="text-sm" for="setup-member-name-{{ $index }}">Legal name<input id="setup-member-name-{{ $index }}" class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" wire:model="setupMembers.{{ $index }}.name">@error("setupMembers.$index.name") <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                                    <label class="text-sm" for="setup-member-email-{{ $index }}">Email<input id="setup-member-email-{{ $index }}" class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="email" wire:model="setupMembers.{{ $index }}.email">@error("setupMembers.$index.email") <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                                    <label class="text-sm" for="setup-member-whatsapp-{{ $index }}">WhatsApp<input id="setup-member-whatsapp-{{ $index }}" class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" wire:model="setupMembers.{{ $index }}.whatsapp">@error("setupMembers.$index.whatsapp") <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                                </div>
                                <div>
                                    <label class="flex cursor-pointer items-center gap-3 border border-dashed p-4 hover:border-catalyst-primary {{ $errors->has("setupMemberKtms.$index") ? 'border-status-error-ink bg-status-error/5' : 'border-catalyst-grey/60 bg-catalyst-neutral/60' }}" for="setup-member-ktm-{{ $index }}">
                                        <svg class="h-6 w-6 shrink-0 text-catalyst-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        <span class="min-w-0"><span class="block text-sm font-semibold">Choose member KTM</span><span class="mt-1 block truncate text-xs text-catalyst-muted">{{ isset($setupMemberKtms[$index]) ? $setupMemberKtms[$index]->getClientOriginalName() : 'JPG, JPEG, or PNG · maximum 2 MB' }}</span></span>
                                        <input id="setup-member-ktm-{{ $index }}" class="sr-only" type="file" wire:model="setupMemberKtms.{{ $index }}" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                                    </label>
                                    <p class="mt-2 text-xs font-medium text-catalyst-primary" wire:loading wire:target="setupMemberKtms.{{ $index }}" role="status">Checking the selected file…</p>
                                    @error("setupMemberKtms.$index") <p class="mt-2 text-sm text-status-error-ink" role="alert">{{ $message }}</p> @enderror
                                </div>
                            </article>
                        @empty
                            <div class="border border-dashed border-catalyst-grey/50 bg-catalyst-neutral/40 p-5 text-center">
                                <p class="text-sm font-medium">No additional members yet</p>
                                <p class="mt-1 text-xs leading-5 text-catalyst-muted">You can create a captain-only team or add up to two members now.</p>
                            </div>
                        @endforelse
                    </section>

                    <footer class="flex flex-col gap-4 border-t border-catalyst-grey/30 bg-catalyst-neutral/50 p-5 sm:flex-row sm:items-center sm:justify-between sm:p-6">
                        <p class="text-sm text-catalyst-ink/70">One submit will save the team and all {{ count($setupMembers) + 1 }} participant{{ count($setupMembers) ? 's' : '' }} together.</p>
                        <button class="bg-catalyst-primary px-5 py-3 text-sm font-medium text-white disabled:cursor-not-allowed disabled:opacity-45" type="submit" wire:loading.attr="disabled" wire:target="createTeam,captainKtm,setupMemberKtms">
                            <span wire:loading.remove wire:target="createTeam">Create team</span>
                            <span wire:loading wire:target="createTeam">Creating team…</span>
                        </button>
                    </footer>
                </form>
            @else
            <form class="space-y-5 border border-catalyst-grey/30 bg-white p-5 sm:p-6" wire:submit="saveTeam">
                <div>
                    <p class="text-xs font-medium uppercase tracking-wider text-catalyst-primary">Step 1</p>
                    <h2 class="mt-2 font-display text-xl font-semibold">Team details</h2>
                    <p class="mt-2 text-sm leading-6 text-catalyst-ink/70">This team will be used across your Catalyst competition registrations.</p>

                    @if ($team?->isLocked())
                        <p class="mt-3 text-sm text-status-warning-ink">Team data is locked because a payment proof has been submitted.</p>
                    @endif
                </div>

                <fieldset class="grid gap-5 sm:grid-cols-2" @disabled($team?->isLocked())>
                    <label class="block text-sm font-medium" for="team-name">
                        Team name
                        <input id="team-name" class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="text" wire:model="teamForm.name" @error('teamForm.name') aria-invalid="true" aria-describedby="team-name-error" @enderror>
                        @error('teamForm.name') <span id="team-name-error" class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span> @enderror
                    </label>
                    <label class="block text-sm font-medium" for="team-institution">
                        Institution
                        <input id="team-institution" class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="text" wire:model="teamForm.institution" @error('teamForm.institution') aria-invalid="true" aria-describedby="team-institution-error" @enderror>
                        @error('teamForm.institution') <span id="team-institution-error" class="mt-1 block text-sm text-status-error-ink">{{ $message }}</span> @enderror
                    </label>
                </fieldset>

                @if (! $team?->isLocked())
                    <button class="bg-catalyst-primary px-4 py-3 text-sm font-medium text-white" type="submit">{{ $team ? 'Save team details' : 'Create team' }}</button>
                @endif
            </form>

                <section class="space-y-5 border border-catalyst-grey/30 bg-white p-5 sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <p class="text-xs font-medium uppercase tracking-wider text-catalyst-primary">Step 2</p>
                            <h2 class="mt-2 font-display text-xl font-semibold">Captain KTM</h2>
                            <p class="mt-2 text-sm text-catalyst-ink/70">{{ $team->captain->name }} · {{ $team->captain->email }}</p>
                        </div>
                        @if ($team->captain->hasCompleteKtm())
                            <a class="text-sm font-medium text-catalyst-primary underline underline-offset-4" href="{{ route('dashboard.private-files.captain-ktm', $team->captain) }}" target="_blank" rel="noopener">View current KTM</a>
                        @endif
                    </div>

                    @if (! $team->isLocked())
                        <form class="space-y-4" wire:submit="uploadCaptainKtm">
                            <div>
                                <label class="flex min-h-32 cursor-pointer flex-col items-center justify-center border border-dashed px-5 py-6 text-center transition-colors hover:border-catalyst-primary hover:bg-catalyst-surface/40 {{ $errors->has('captainKtm') ? 'border-status-error-ink bg-status-error/5' : 'border-catalyst-grey/60 bg-catalyst-neutral/60' }}" for="captain-ktm">
                                    <svg class="h-7 w-7 text-catalyst-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true">
                                        <path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span class="mt-3 text-sm font-semibold text-catalyst-ink">{{ $team->captain->hasCompleteKtm() ? 'Choose a replacement KTM' : 'Choose the captain KTM' }}</span>
                                    <span class="mt-1 text-xs leading-5 text-catalyst-muted">JPG, JPEG, or PNG · maximum 2 MB</span>
                                    <input id="captain-ktm" class="sr-only" type="file" wire:model="captainKtm" accept=".jpg,.jpeg,.png,image/jpeg,image/png" aria-describedby="captain-ktm-help @error('captainKtm') captain-ktm-error @enderror" @error('captainKtm') aria-invalid="true" @enderror>
                                </label>
                                <p id="captain-ktm-help" class="mt-2 text-xs leading-5 text-catalyst-muted">Selecting a file prepares a private preview. Use the save button below to attach it to the captain profile.</p>
                                <p class="mt-2 text-xs font-medium text-catalyst-primary" wire:loading wire:target="captainKtm" role="status">Checking the selected file…</p>
                                @error('captainKtm') <p id="captain-ktm-error" class="mt-2 text-sm text-status-error-ink" role="alert">{{ $message }}</p> @enderror
                            </div>

                            @if ($captainKtm && ! $errors->has('captainKtm'))
                                <div class="grid gap-4 border border-catalyst-grey/30 p-4 sm:grid-cols-[7rem_1fr] sm:items-center">
                                    <img class="h-28 w-full bg-catalyst-neutral object-contain" src="{{ $captainKtm->temporaryUrl() }}" alt="Selected captain KTM preview">
                                    <div class="min-w-0">
                                        <p class="truncate text-sm font-semibold">{{ $captainKtm->getClientOriginalName() }}</p>
                                        <p class="mt-1 text-xs text-catalyst-muted">{{ number_format($captainKtm->getSize() / 1024) }} KB · ready to save</p>
                                    </div>
                                </div>
                            @endif

                            <button class="bg-catalyst-primary px-4 py-3 text-sm font-medium text-white disabled:cursor-not-allowed disabled:opacity-45" type="submit" @disabled(! $captainKtm || $errors->has('captainKtm')) wire:loading.attr="disabled" wire:target="captainKtm,uploadCaptainKtm">
                                <span wire:loading.remove wire:target="uploadCaptainKtm">{{ $team->captain->hasCompleteKtm() ? 'Save replacement KTM' : 'Save captain KTM' }}</span>
                                <span wire:loading wire:target="uploadCaptainKtm">Saving KTM…</span>
                            </button>
                        </form>
                    @endif
                </section>

                <section class="space-y-6 border border-catalyst-grey/30 bg-white p-5 sm:p-6">
                    <div>
                        <p class="text-xs font-medium uppercase tracking-wider text-catalyst-primary">Step 3</p>
                        <h2 class="mt-2 font-display text-xl font-semibold">Team members</h2>
                        <p class="mt-2 text-sm leading-6 text-catalyst-ink/70">The captain is already included. You may add up to two additional members.</p>
                    </div>

                    <div class="space-y-4">
                        @forelse ($team->members as $member)
                            <article class="border border-catalyst-grey/30 p-4" wire:key="member-{{ $member->id }}">
                                @if ($editingMemberId === $member->id)
                                    <form class="space-y-4" wire:submit="updateMember">
                                        @error('editMemberForm') <p class="border border-status-error/30 bg-status-error/5 p-3 text-sm text-status-error-ink" role="alert">{{ $message }}</p> @enderror
                                        <div class="grid gap-4 sm:grid-cols-3">
                                            <label class="text-sm" for="edit-member-name-{{ $member->id }}">Legal name<input id="edit-member-name-{{ $member->id }}" class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" wire:model="editMemberForm.name">@error('editMemberForm.name') <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                                            <label class="text-sm" for="edit-member-email-{{ $member->id }}">Email<input id="edit-member-email-{{ $member->id }}" class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="email" wire:model="editMemberForm.email">@error('editMemberForm.email') <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                                            <label class="text-sm" for="edit-member-whatsapp-{{ $member->id }}">WhatsApp<input id="edit-member-whatsapp-{{ $member->id }}" class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" wire:model="editMemberForm.whatsapp">@error('editMemberForm.whatsapp') <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                                        </div>
                                        <div>
                                            <label class="flex cursor-pointer items-center gap-3 border border-dashed border-catalyst-grey/60 bg-catalyst-neutral/60 p-4 hover:border-catalyst-primary" for="edit-member-ktm-{{ $member->id }}">
                                                <svg class="h-6 w-6 shrink-0 text-catalyst-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                                <span><span class="block text-sm font-semibold">Replace KTM (optional)</span><span class="mt-1 block text-xs text-catalyst-muted">JPG, JPEG, or PNG · maximum 2 MB</span></span>
                                                <input id="edit-member-ktm-{{ $member->id }}" class="sr-only" type="file" wire:model="editMemberKtm" accept=".jpg,.jpeg,.png,image/jpeg,image/png">
                                            </label>
                                            <p class="mt-2 text-xs font-medium text-catalyst-primary" wire:loading wire:target="editMemberKtm" role="status">Checking the selected file…</p>
                                            @if ($editMemberKtm && ! $errors->has('editMemberKtm')) <p class="mt-2 truncate text-xs text-catalyst-muted">Selected: {{ $editMemberKtm->getClientOriginalName() }}</p> @endif
                                            @error('editMemberKtm') <p class="mt-2 text-sm text-status-error-ink" role="alert">{{ $message }}</p> @enderror
                                        </div>
                                        <div class="flex flex-wrap gap-3">
                                            <button class="bg-catalyst-primary px-4 py-3 text-sm font-medium text-white" type="submit"><span wire:loading.remove wire:target="updateMember">Save member</span><span wire:loading wire:target="updateMember">Saving…</span></button>
                                            <button class="border border-catalyst-grey/50 px-4 py-3 text-sm" type="button" wire:click="cancelEditingMember">Cancel</button>
                                        </div>
                                    </form>
                                @else
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                        <div><h3 class="font-medium">{{ $member->name }}</h3><p class="mt-1 text-sm text-catalyst-ink/70">{{ $member->email }} · {{ $member->whatsapp }}</p></div>
                                        <div class="flex flex-wrap gap-3">
                                            <a class="text-sm font-medium text-catalyst-primary underline underline-offset-4" href="{{ route('dashboard.private-files.team-member-ktm', $member) }}" target="_blank" rel="noopener">View KTM</a>
                                            @if (! $team->isLocked())
                                                <button class="text-sm font-medium" type="button" wire:click="startEditingMember({{ $member->id }})">Edit</button>
                                                <button class="text-sm font-medium text-status-error-ink" type="button" wire:click="removeMember({{ $member->id }})" wire:confirm="Remove this member and permanently delete their KTM?">Remove</button>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </article>
                        @empty
                            <p class="border-y border-catalyst-grey/20 py-4 text-sm text-catalyst-muted">No additional members yet. Your captain already counts as the first person in the team.</p>
                        @endforelse
                    </div>

                    @if (! $team->isLocked() && $team->members->count() < 2)
                        <form class="space-y-5 border-t border-catalyst-grey/30 pt-6" wire:submit="addMember">
                            <div><h3 class="font-display text-lg font-semibold">Add a member</h3><p class="mt-1 text-sm text-catalyst-muted">The member record and KTM are saved together when you select Add member.</p></div>
                            @error('memberForm') <p class="border border-status-error/30 bg-status-error/5 p-3 text-sm text-status-error-ink" role="alert">{{ $message }}</p> @enderror
                            <div class="grid gap-4 sm:grid-cols-3">
                                <label class="text-sm" for="member-name">Legal name<input id="member-name" class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" wire:model="memberForm.name" @error('memberForm.name') aria-invalid="true" @enderror>@error('memberForm.name') <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                                <label class="text-sm" for="member-email">Email<input id="member-email" class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" type="email" wire:model="memberForm.email" @error('memberForm.email') aria-invalid="true" @enderror>@error('memberForm.email') <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                                <label class="text-sm" for="member-whatsapp">WhatsApp<input id="member-whatsapp" class="mt-2 w-full border border-catalyst-grey/50 px-3 py-3" wire:model="memberForm.whatsapp" @error('memberForm.whatsapp') aria-invalid="true" @enderror>@error('memberForm.whatsapp') <span class="mt-1 block text-status-error-ink">{{ $message }}</span> @enderror</label>
                            </div>
                            <div>
                                <label class="flex min-h-32 cursor-pointer flex-col items-center justify-center border border-dashed px-5 py-6 text-center transition-colors hover:border-catalyst-primary hover:bg-catalyst-surface/40 {{ $errors->has('memberKtm') ? 'border-status-error-ink bg-status-error/5' : 'border-catalyst-grey/60 bg-catalyst-neutral/60' }}" for="member-ktm">
                                    <svg class="h-7 w-7 text-catalyst-primary" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M12 16V4m0 0L7.5 8.5M12 4l4.5 4.5M5 14v4a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-4" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                    <span class="mt-3 text-sm font-semibold text-catalyst-ink">Choose the member KTM</span>
                                    <span class="mt-1 text-xs leading-5 text-catalyst-muted">JPG, JPEG, or PNG · maximum 2 MB</span>
                                    <input id="member-ktm" class="sr-only" type="file" wire:model="memberKtm" accept=".jpg,.jpeg,.png,image/jpeg,image/png" @error('memberKtm') aria-invalid="true" aria-describedby="member-ktm-error" @enderror>
                                </label>
                                <p class="mt-2 text-xs font-medium text-catalyst-primary" wire:loading wire:target="memberKtm" role="status">Checking the selected file…</p>
                                @error('memberKtm') <p id="member-ktm-error" class="mt-2 text-sm text-status-error-ink" role="alert">{{ $message }}</p> @enderror
                            </div>
                            @if ($memberKtm && ! $errors->has('memberKtm'))
                                <div class="grid gap-4 border border-catalyst-grey/30 p-4 sm:grid-cols-[7rem_1fr] sm:items-center">
                                    <img class="h-28 w-full bg-catalyst-neutral object-contain" src="{{ $memberKtm->temporaryUrl() }}" alt="Selected member KTM preview">
                                    <div class="min-w-0"><p class="truncate text-sm font-semibold">{{ $memberKtm->getClientOriginalName() }}</p><p class="mt-1 text-xs text-catalyst-muted">{{ number_format($memberKtm->getSize() / 1024) }} KB · will be saved with this member</p></div>
                                </div>
                            @endif
                            <button class="bg-catalyst-primary px-4 py-3 text-sm font-medium text-white disabled:cursor-not-allowed disabled:opacity-45" type="submit" wire:loading.attr="disabled" wire:target="memberKtm,addMember">
                                <span wire:loading.remove wire:target="addMember">Add member</span><span wire:loading wire:target="addMember">Saving member and KTM…</span>
                            </button>
                        </form>
                    @elseif (! $team->isLocked())
                        <p class="border-t border-catalyst-grey/30 pt-5 text-sm text-catalyst-muted">This team has reached the maximum of three people including the captain.</p>
                    @endif
                </section>
            @endif
        </div>
    </x-ui.container>
</div>
