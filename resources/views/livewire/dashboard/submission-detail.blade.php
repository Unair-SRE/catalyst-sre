<div
    x-data="{
        clientErrors: [],
        selectFiles(fileList) {
            this.clientErrors = [];
            const allowed = @js($state['requirements']['allowed_extensions']);
            const maxBytes = {{ $state['requirements']['max_file_bytes'] }};
            const maxFiles = {{ $state['requirements']['max_files'] }};
            const remaining = maxFiles - {{ count($state['files']) }};
            const accepted = [];

            Array.from(fileList).forEach((file) => {
                const extension = file.name.split('.').pop().toLowerCase();

                if (!allowed.includes(extension)) {
                    this.clientErrors.push(`Unsupported format: ${file.name}. Upload a {{ $state['requirements']['allowed_label'] }} file.`);
                    return;
                }

                if (file.size > maxBytes) {
                    this.clientErrors.push(`File too large: ${file.name} exceeds the {{ $state['requirements']['max_file_label'] }} limit. Please compress it and try again.`);
                    return;
                }

                if (accepted.length >= remaining) {
                    this.clientErrors.push(`Maximum files reached. You can upload up to ${maxFiles} files for this stage.`);
                    return;
                }

                accepted.push({
                    id: window.crypto?.randomUUID?.() ?? `${Date.now()}-${accepted.length}`,
                    name: file.name,
                    type: file.type,
                    size_bytes: file.size,
                });
            });

            if (accepted.length) {
                $wire.addFiles(accepted);
                accepted.forEach((file) => window.setTimeout(() => $wire.finishUpload(file.id), 700));
            }

            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }
        },
    }"
    x-on:submission-upload-ready.window="window.setTimeout(() => $wire.finishUpload($event.detail.id), 700)"
>
    <x-ui.container class="py-8 sm:py-10 lg:py-14">
        <div class="space-y-8 lg:space-y-10">
            <header class="dashboard-page-heading max-w-4xl">
                <a class="inline-flex items-center gap-2 text-sm font-medium text-catalyst-ink/75 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-catalyst-primary" href="{{ route('dashboard.submission.index') }}">
                    <svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m11.5 4-6 6 6 6M6 10h8.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" /></svg>
                    Back to Submission
                </a>
                <p class="mt-8 text-sm font-medium tracking-wide text-catalyst-muted">{{ $state['competition']['short_name'] }} · {{ $state['stage']['name'] }}</p>
                <h1 class="mt-3 font-display text-3xl font-medium leading-tight tracking-tight text-catalyst-ink sm:text-4xl xl:text-5xl">{{ $state['competition']['name'] }}</h1>
                <p class="mt-4 text-base leading-7 text-catalyst-ink/80 sm:text-lg">Review your files and submit your entry before the deadline.</p>
            </header>

            <section class="border border-catalyst-grey/30 bg-white p-5 sm:p-6" aria-label="Submission status overview">
                <dl class="grid gap-5 sm:grid-cols-3 sm:divide-x sm:divide-catalyst-grey/30">
                    <div class="sm:pr-5">
                        <dt class="text-xs font-medium tracking-wide text-catalyst-muted">Registration</dt>
                        <dd class="mt-2"><x-dashboard.status-pill :label="$state['registration']['label']" :tone="$state['registration']['tone']" /></dd>
                    </div>
                    <div class="sm:px-5">
                        <dt class="text-xs font-medium tracking-wide text-catalyst-muted">Stage</dt>
                        <dd class="mt-2"><x-dashboard.status-pill :label="$state['stage_lifecycle']['label']" :tone="$state['stage_lifecycle']['tone']" /></dd>
                    </div>
                    <div class="sm:pl-5">
                        <dt class="text-xs font-medium tracking-wide text-catalyst-muted">Submission</dt>
                        <dd class="mt-2"><x-dashboard.status-pill :label="$state['submission']['label']" :tone="$state['submission']['tone']" /></dd>
                    </div>
                </dl>
            </section>

            @if (count($state['stages']) > 1)
                <nav class="border border-catalyst-grey/30 bg-white p-2" aria-label="Submission stages">
                    <div class="flex gap-2 overflow-x-auto">
                        @foreach ($state['stages'] as $stage)
                            <a class="shrink-0 px-3 py-2 text-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary {{ $stage['slug'] === $state['stage']['slug'] ? 'bg-catalyst-primary font-medium text-white' : 'text-catalyst-ink/75' }}" href="{{ route('dashboard.submission.show', ['competition' => $state['competition']['slug'], 'stage' => $stage['slug'], 'scenario' => $state['scenario']]) }}" @if ($stage['slug'] === $state['stage']['slug']) aria-current="page" @endif>{{ $stage['name'] }}</a>
                        @endforeach
                    </div>
                </nav>
            @endif

            @if ($state['submission_access'] === 'locked')
                <section class="border border-status-info/30 bg-status-info/5 p-5 sm:p-6" aria-labelledby="submission-locked-heading">
                    <h2 id="submission-locked-heading" class="font-display text-xl font-semibold tracking-tight">Submission locked</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-catalyst-ink/75">Your registration must be verified before this submission stage becomes available.</p>
                    <a class="mt-4 inline-flex min-h-11 items-center justify-center border border-catalyst-primary px-4 py-3 text-sm font-medium text-catalyst-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" href="{{ route('dashboard.registration.show', ['competition' => $state['competition']['slug']]) }}">View Registration</a>
                </section>
            @elseif ($state['stage_state'] === 'upcoming')
                <section class="border border-catalyst-grey/30 bg-white p-5 sm:p-6" aria-labelledby="submission-upcoming-heading">
                    <h2 id="submission-upcoming-heading" class="font-display text-xl font-semibold tracking-tight">Submission opens soon</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-catalyst-ink/75">{{ $state['stage']['name'] }} will open on <time datetime="{{ $state['stage']['open_at'] }}">{{ $state['stage']['open_label'] }}</time>. This server-rendered date remains the source of truth for the prototype.</p>
                </section>
            @elseif ($state['stage_state'] === 'closed')
                <section class="border border-catalyst-grey/30 bg-white p-5 sm:p-6" aria-labelledby="submission-closed-heading">
                    <h2 id="submission-closed-heading" class="font-display text-xl font-semibold tracking-tight">Submission closed</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-catalyst-ink/75">The submission period for this stage has ended. The deadline was <time datetime="{{ $state['stage']['deadline_at'] }}">{{ $state['stage']['deadline_label'] }}</time>@if ($state['stage']['late_close_label']), and the late window closed {{ $state['stage']['late_close_label'] }}@endif.</p>
                    @if (! $state['active_submission_at'])
                        <a class="mt-4 inline-flex min-h-11 items-center justify-center border border-catalyst-primary px-4 py-3 text-sm font-medium text-catalyst-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" href="{{ $state['contact_url'] }}">Contact Catalyst</a>
                    @endif
                </section>
            @elseif ($state['submission_state'] === 'revision_required')
                <section class="border border-status-warning/40 bg-status-warning/5 p-5 sm:p-6" aria-labelledby="submission-revision-heading">
                    <h2 id="submission-revision-heading" class="font-display text-xl font-semibold tracking-tight">Your submission needs an update</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-catalyst-ink/75">{{ $state['revision_note'] }}</p>
                </section>
            @elseif ($state['submission_state'] === 'accepted')
                <section class="border border-status-success/30 bg-status-success/5 p-5 sm:p-6" aria-labelledby="submission-accepted-heading">
                    <h2 id="submission-accepted-heading" class="font-display text-xl font-semibold tracking-tight">Submission accepted</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-catalyst-ink/75">Catalyst has administratively accepted the latest active submission.</p>
                </section>
            @elseif (in_array($state['submission_state'], ['submitted', 'late'], true))
                <section class="border p-5 sm:p-6 {{ $state['submission_state'] === 'late' ? 'border-status-warning/30 bg-status-warning/5' : 'border-status-success/30 bg-status-success/5' }}" aria-labelledby="submission-received-heading">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 id="submission-received-heading" class="font-display text-xl font-semibold tracking-tight">{{ $state['submission_state'] === 'late' ? 'Submitted late' : 'Submission received' }}</h2>
                            <p class="mt-2 text-sm leading-6 text-catalyst-ink/75">Submitted <time datetime="{{ $state['active_submission_at'] }}">{{ $state['active_submission_label'] }}</time>@if ($state['lateness']) · {{ $state['lateness'] }}@endif.</p>
                            @if ($can_unsubmit)
                                <p class="mt-2 text-sm leading-6 text-catalyst-ink/75">You can unsubmit and update your files while submissions are still accepted.</p>
                            @endif
                        </div>
                        @if ($can_unsubmit)
                            <button class="inline-flex min-h-11 shrink-0 items-center justify-center border border-catalyst-primary px-4 py-3 text-sm font-medium text-catalyst-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="openUnsubmitConfirmation">Unsubmit</button>
                        @endif
                    </div>
                </section>
            @endif

            <section class="border border-catalyst-grey/30 bg-white p-5 sm:p-6" aria-labelledby="submission-requirements-heading">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                    <div>
                        <h2 id="submission-requirements-heading" class="font-display text-xl font-semibold tracking-tight">Submission Requirements</h2>
                        <p class="mt-2 text-sm leading-6 text-catalyst-ink/75">These prototype limits are configured per stage and can be replaced by production rules later.</p>
                    </div>
                    @if ($state['stage_state'] === 'late_window')
                        <x-dashboard.status-pill label="Late submissions accepted" tone="warning" />
                    @endif
                </div>
                <dl class="mt-6 grid gap-5 text-sm sm:grid-cols-2 lg:grid-cols-4">
                    <div><dt class="text-catalyst-muted">Formats</dt><dd class="mt-1 font-medium">{{ $state['requirements']['allowed_label'] }}</dd></div>
                    <div><dt class="text-catalyst-muted">File size</dt><dd class="mt-1 font-medium">{{ $state['requirements']['max_file_label'] }}</dd></div>
                    <div><dt class="text-catalyst-muted">Maximum files</dt><dd class="mt-1 font-medium">{{ $state['requirements']['max_files'] }} files</dd></div>
                    <div><dt class="text-catalyst-muted">Deadline</dt><dd class="mt-1 font-medium"><time datetime="{{ $state['stage']['deadline_at'] }}">{{ $state['stage']['deadline_label'] }}</time></dd></div>
                </dl>
                @if ($state['stage']['late_close_label'])
                    <p class="mt-5 text-sm leading-6 text-catalyst-ink/70">Late window closes {{ $state['stage']['late_close_label'] }}. Compress files before uploading if they exceed the stated limit.</p>
                @endif
            </section>

            @if ($can_edit)
                <section class="border border-catalyst-grey/30 bg-white p-5 sm:p-6" aria-labelledby="upload-heading">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <h2 id="upload-heading" class="font-display text-xl font-semibold tracking-tight">Upload files</h2>
                            <p class="mt-2 text-sm leading-6 text-catalyst-ink/75">Files stay in this browser-driven prototype and are not sent to permanent storage.</p>
                        </div>
                        <p class="text-xs font-medium tracking-wide text-catalyst-muted">{{ count($state['files']) }} / {{ $state['requirements']['max_files'] }} files</p>
                    </div>

                    <input x-ref="fileInput" class="sr-only" type="file" multiple accept=".pdf,.ppt,.pptx,application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation" @change="selectFiles($event.target.files)">
                    <div class="mt-5 grid min-h-52 place-items-center border border-dashed border-catalyst-primary/35 bg-catalyst-neutral p-6 text-center" @drop.prevent="selectFiles($event.dataTransfer.files)" @dragover.prevent>
                        <div>
                            <svg class="mx-auto mb-4 size-7 text-catalyst-primary" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 16V4m0 0L7 9m5-5 5 5M4 16v4h16v-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            <p class="font-medium text-catalyst-ink">Drop your files here</p>
                            <p class="mt-2 text-sm text-catalyst-ink/70">or choose up to {{ $state['requirements']['max_files'] }} {{ strtolower($state['requirements']['allowed_label']) }} files.</p>
                            <button class="mt-5 inline-flex min-h-11 items-center justify-center bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" @click="$refs.fileInput.click()">Choose files</button>
                        </div>
                    </div>

                    <template x-if="clientErrors.length">
                        <div class="mt-4 space-y-2" aria-live="polite">
                            <template x-for="error in clientErrors" :key="error"><p class="border border-status-error/30 bg-status-error/5 p-3 text-sm leading-6 text-catalyst-ink"><span class="font-medium text-status-error-ink">Upload issue.</span> <span x-text="error"></span></p></template>
                        </div>
                    </template>

                    @if ($uploadErrors !== [])
                        <div class="mt-4 space-y-2" aria-live="polite">
                            @foreach ($uploadErrors as $error)
                                <p class="border border-status-error/30 bg-status-error/5 p-3 text-sm leading-6 text-catalyst-ink"><span class="font-medium text-status-error-ink">Upload issue.</span> {{ $error }}</p>
                            @endforeach
                        </div>
                    @endif
                </section>
            @endif

            @if ($state['files'] !== [])
                <section class="border border-catalyst-grey/30 bg-white p-5 sm:p-6" aria-labelledby="selected-files-heading">
                    <h2 id="selected-files-heading" class="font-display text-xl font-semibold tracking-tight">{{ in_array($state['submission_state'], ['submitted', 'late', 'accepted'], true) ? 'Active files' : 'Selected files' }}</h2>
                    <ul class="mt-5 divide-y divide-catalyst-grey/30">
                        @foreach ($state['files'] as $file)
                            <li class="py-4 first:pt-0 last:pb-0" wire:key="submission-file-{{ $file['id'] }}">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div class="min-w-0">
                                        <p class="break-all text-sm font-medium text-catalyst-ink">{{ $file['name'] }}</p>
                                        <p class="mt-1 text-xs text-catalyst-muted">{{ $file['size_label'] }} · {{ strtoupper(pathinfo($file['name'], PATHINFO_EXTENSION)) }}</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        @if ($file['state'] === 'uploaded')
                                            <span class="text-sm font-medium text-status-success-ink">Uploaded</span>
                                        @elseif ($file['state'] === 'uploading')
                                            <span class="text-sm font-medium text-catalyst-ink/75">Uploading {{ $file['progress'] }}%</span>
                                        @else
                                            <span class="text-sm font-medium text-status-error-ink">Upload interrupted</span>
                                        @endif
                                        @if ($can_edit)
                                            <button class="inline-flex min-h-11 min-w-11 items-center justify-center text-sm font-medium text-catalyst-primary underline underline-offset-4 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="removeFile('{{ $file['id'] }}')" aria-label="Remove {{ $file['name'] }}">Remove</button>
                                        @endif
                                    </div>
                                </div>
                                @if ($file['state'] === 'uploading')
                                    <div class="mt-3 h-1.5 overflow-hidden bg-catalyst-grey/20" role="progressbar" aria-label="Uploading {{ $file['name'] }}" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $file['progress'] }}"><div class="h-full bg-catalyst-primary transition-[width] duration-500 motion-reduce:transition-none" style="width: {{ $file['progress'] }}%"></div></div>
                                @elseif ($file['state'] === 'failed')
                                    <div class="mt-3 flex flex-col gap-3 border-l-2 border-status-error pl-3 text-sm leading-6 text-catalyst-ink/75 sm:flex-row sm:items-center sm:justify-between"><p>Please check your connection and try again.</p>@if ($can_edit)<button class="w-fit font-medium text-catalyst-primary underline underline-offset-4 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="retryUpload('{{ $file['id'] }}')">Retry upload</button>@endif</div>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif

            @if ($feedback)
                <p class="border border-status-info/30 bg-status-info/5 p-4 text-sm leading-6 text-catalyst-ink" aria-live="polite">{{ $feedback }}</p>
            @endif

            @if ($can_edit)
                <button class="inline-flex min-h-12 w-full sm:w-auto items-center justify-center bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="openSubmitConfirmation">{{ $state['submission_state'] === 'revision_required' ? 'Submit Updated Entry' : 'Submit Entry' }}</button>
            @endif

            @if ($state['history'] !== [])
                <section class="border border-catalyst-grey/30 bg-white p-5 sm:p-6" aria-labelledby="submission-history-heading">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div><h2 id="submission-history-heading" class="font-display text-xl font-semibold tracking-tight">Submission History</h2><p class="mt-2 text-sm leading-6 text-catalyst-ink/75">Previous versions stay recorded when you unsubmit or resubmit.</p></div>
                        <button class="shrink-0 text-sm font-medium text-catalyst-primary underline underline-offset-4 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="toggleHistory">{{ $showHistory ? 'Hide history' : 'View Submission History' }}</button>
                    </div>
                    @if ($showHistory)
                        <ol class="mt-5 space-y-4 border-catalyst-grey/30 border-l pl-5">
                            @foreach ($state['history'] as $event)
                                <li class="relative"><span class="absolute -left-[1.7rem] top-1.5 size-2.5 rounded-full bg-catalyst-primary" aria-hidden="true"></span><p class="text-sm font-medium">{{ $event['event'] }}</p><p class="mt-1 text-sm text-catalyst-ink/75">{{ $event['detail'] }}</p><time class="mt-1 block text-xs text-catalyst-muted" datetime="{{ $event['timestamp'] }}">{{ $event['timestamp_label'] }}</time></li>
                            @endforeach
                        </ol>
                    @endif
                </section>
            @endif

            <details class="border border-catalyst-grey/30 bg-white p-4">
                <summary class="cursor-pointer text-sm font-medium text-catalyst-ink focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary">Prototype state</summary>
                <div class="mt-4 max-w-xs">
                    <label class="block text-sm text-catalyst-ink/80" for="submission-detail-scenario">Review scenario</label>
                    <select id="submission-detail-scenario" class="mt-2 w-full border border-catalyst-grey/50 bg-white px-3 py-2 text-sm text-catalyst-ink focus:border-catalyst-primary focus:outline-none" wire:model.live="scenario">
                        @foreach ($scenarios as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
            </details>
        </div>
    </x-ui.container>

    @if ($showSubmitConfirm)
        <dialog x-data x-init="$el.showModal()" @cancel.prevent="$wire.cancelSubmitConfirmation()" class="fixed inset-0 m-auto max-h-[90dvh] w-[calc(100%_-_2.5rem)] max-w-md overflow-y-auto border-0 bg-white p-0 text-catalyst-ink backdrop:bg-catalyst-ink/40" aria-labelledby="submit-entry-title">
            <div class="w-full max-w-md border border-catalyst-grey/30 bg-white p-6 shadow-lg">
                <h2 id="submit-entry-title" class="font-display text-2xl font-semibold tracking-tight">Submit your entry?</h2>
                <p class="mt-3 text-sm leading-6 text-catalyst-ink/75">Make sure your files are final. You can unsubmit and replace them while the submission window is still available, but your latest active submission timestamp will be used.</p>
                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"><button class="border border-catalyst-grey/50 px-4 py-3 text-sm font-medium focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="cancelSubmitConfirmation">Cancel</button><button class="bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="submitEntry">Submit</button></div>
            </div>
        </dialog>
    @endif

    @if ($showUnsubmitConfirm)
        <dialog x-data x-init="$el.showModal()" @cancel.prevent="$wire.cancelUnsubmitConfirmation()" class="fixed inset-0 m-auto max-h-[90dvh] w-[calc(100%_-_2.5rem)] max-w-md overflow-y-auto border-0 bg-white p-0 text-catalyst-ink backdrop:bg-catalyst-ink/40" aria-labelledby="unsubmit-entry-title">
            <div class="w-full max-w-md border border-catalyst-grey/30 bg-white p-6 shadow-lg">
                <h2 id="unsubmit-entry-title" class="font-display text-2xl font-semibold tracking-tight">Unsubmit this entry?</h2>
                <p class="mt-3 text-sm leading-6 text-catalyst-ink/75">Your current submission will no longer be active. Its history will remain recorded, and you can upload changes and submit again while the submission window remains available.</p>
                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end"><button class="border border-catalyst-grey/50 px-4 py-3 text-sm font-medium focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="cancelUnsubmitConfirmation">Cancel</button><button class="bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" type="button" wire:click="unsubmitEntry">Unsubmit</button></div>
            </div>
        </dialog>
    @endif
</div>
