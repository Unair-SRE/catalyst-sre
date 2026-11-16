<div
    x-data="{
        uploading: false,
        progress: 0,
        error: null,
        fileName: '',
        maxBytes: {{ \App\Actions\Submissions\RequestSubmissionUpload::MAX_BYTES }},
        allowed: @js(\App\Actions\Submissions\RequestSubmissionUpload::ALLOWED_FORMATS),
        async upload(file) {
            this.error = null;
            const extension = file.name.split('.').pop().toLowerCase();

            if (!this.allowed.includes(extension)) {
                this.error = 'Only PDF, PPT, or PPTX files are accepted.';
                return;
            }

            if (file.size > this.maxBytes) {
                this.error = 'The file must not exceed 5 MB.';
                return;
            }

            this.uploading = true;
            this.progress = 0;
            this.fileName = file.name;

            try {
                const authorization = await $wire.requestUpload(extension);

                if (!authorization.ok) {
                    this.error = authorization.message;
                    return;
                }

                const form = new FormData();
                form.append('file', file);
                Object.entries(authorization.params).forEach(([key, value]) => form.append(key, value));

                const uploaded = await this.send(
                    `https://api.cloudinary.com/v1_1/${authorization.cloud_name}/${authorization.resource_type}/upload`,
                    form,
                );
                const confirmed = await $wire.confirmUpload(uploaded.public_id);

                if (!confirmed.ok) {
                    this.error = confirmed.message;
                }
            } catch (exception) {
                this.error = 'Upload failed. Check your connection and try again.';
            } finally {
                this.uploading = false;
            }
        },
        send(url, form) {
            return new Promise((resolve, reject) => {
                const request = new XMLHttpRequest();
                request.open('POST', url);
                request.upload.onprogress = (event) => {
                    if (event.lengthComputable) {
                        this.progress = Math.round((event.loaded / event.total) * 100);
                    }
                };
                request.onload = () => {
                    if (request.status >= 200 && request.status < 300) {
                        resolve(JSON.parse(request.responseText));
                    } else {
                        reject(new Error(request.responseText));
                    }
                };
                request.onerror = () => reject(new Error('Network error'));
                request.send(form);
            });
        },
    }"
>
    <x-ui.container class="py-8 sm:py-10 lg:py-14">
        <div class="space-y-8 lg:space-y-10">
            <header class="dashboard-page-heading max-w-4xl">
                <a class="inline-flex items-center gap-2 text-sm font-medium text-catalyst-ink/75 focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-catalyst-primary" href="{{ route('dashboard.submission.index') }}">
                    <svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m11.5 4-6 6 6 6M6 10h8.5" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" /></svg>
                    Back to Submission
                </a>
                <p class="mt-8 text-sm font-medium tracking-wide text-catalyst-muted">{{ $competitionModel->code->value }} · {{ $round->name }}</p>
                <h1 class="mt-3 font-display text-3xl font-medium leading-tight tracking-tight text-catalyst-ink sm:text-4xl xl:text-5xl">{{ $competitionModel->code->label() }}</h1>
                <p class="mt-4 text-base leading-7 text-catalyst-ink/80 sm:text-lg">Review your entry and submit the final file before the deadline.</p>
            </header>

            <section class="border border-catalyst-grey/30 bg-white p-5 sm:p-6" aria-label="Submission status overview">
                <dl class="grid gap-5 sm:grid-cols-3 sm:divide-x sm:divide-catalyst-grey/30">
                    <div class="sm:pr-5">
                        <dt class="text-xs font-medium tracking-wide text-catalyst-muted">Registration</dt>
                        <dd class="mt-2"><x-dashboard.status-pill :label="$registration['label']" :tone="$registration['tone']" /></dd>
                    </div>
                    <div class="sm:px-5">
                        <dt class="text-xs font-medium tracking-wide text-catalyst-muted">Stage</dt>
                        <dd class="mt-2"><x-dashboard.status-pill :label="$stageStatus['label']" :tone="$stageStatus['tone']" /></dd>
                    </div>
                    <div class="sm:pl-5">
                        <dt class="text-xs font-medium tracking-wide text-catalyst-muted">Submission</dt>
                        <dd class="mt-2">
                            @if ($submission)
                                <x-dashboard.status-pill label="Submitted" tone="success" />
                            @else
                                <x-dashboard.status-pill label="Not submitted" />
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>

            @if ($rounds->count() > 1)
                <nav class="border border-catalyst-grey/30 bg-white p-2" aria-label="Submission stages">
                    <div class="flex gap-2 overflow-x-auto">
                        @foreach ($rounds as $item)
                            <a class="shrink-0 px-3 py-2 text-sm focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary {{ $item->is($round) ? 'bg-catalyst-primary font-medium text-white' : 'text-catalyst-ink/75' }}" href="{{ route('dashboard.submission.show', ['competition' => $competition, 'stage' => $item->round_order]) }}" @if ($item->is($round)) aria-current="page" @endif>{{ $item->name }}</a>
                        @endforeach
                    </div>
                </nav>
            @endif

            @if ($state === 'locked')
                <section class="border border-status-info/30 bg-status-info/5 p-5 sm:p-6" aria-labelledby="submission-locked-heading">
                    <h2 id="submission-locked-heading" class="font-display text-xl font-semibold tracking-tight">Submission locked</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-catalyst-ink/75">Your registration must be verified before this stage becomes available.</p>
                    <a class="mt-4 inline-flex min-h-11 items-center justify-center border border-catalyst-primary px-4 py-3 text-sm font-medium text-catalyst-primary focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary" href="{{ route('dashboard.registration.show', ['competition' => $competition]) }}">View Registration</a>
                </section>
            @elseif ($state === 'unscheduled')
                <section class="border border-catalyst-grey/30 bg-white p-5 sm:p-6" aria-labelledby="submission-unscheduled-heading">
                    <h2 id="submission-unscheduled-heading" class="font-display text-xl font-semibold tracking-tight">Submission not scheduled</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-catalyst-ink/75">The committee has not announced the submission window for this stage yet.</p>
                </section>
            @elseif ($state === 'upcoming')
                <section class="border border-catalyst-grey/30 bg-white p-5 sm:p-6" aria-labelledby="submission-upcoming-heading">
                    <h2 id="submission-upcoming-heading" class="font-display text-xl font-semibold tracking-tight">Submission opens soon</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-catalyst-ink/75">This stage opens on <time datetime="{{ $round->starts_at->toIso8601String() }}">{{ $round->starts_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</time>.</p>
                </section>
            @elseif ($state === 'closed')
                <section class="border border-catalyst-grey/30 bg-white p-5 sm:p-6" aria-labelledby="submission-closed-heading">
                    <h2 id="submission-closed-heading" class="font-display text-xl font-semibold tracking-tight">Submission closed</h2>
                    <p class="mt-2 max-w-2xl text-sm leading-6 text-catalyst-ink/75">The submission window closed on <time datetime="{{ $round->ends_at->toIso8601String() }}">{{ $round->ends_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</time>.</p>
                </section>
            @endif

            @if ($fileLabel)
                <section class="border border-status-success/30 bg-status-success/5 p-5 sm:p-6" aria-labelledby="submission-file-heading">
                    <h2 id="submission-file-heading" class="font-display text-xl font-semibold tracking-tight">{{ $state === 'open' ? 'Current submission' : 'Submitted entry' }}</h2>
                    <p class="mt-2 break-all text-sm font-medium text-catalyst-ink">{{ $fileLabel['name'] }}</p>
                    <p class="mt-1 text-sm leading-6 text-catalyst-ink/75">{{ $fileLabel['format'] }} · {{ $fileLabel['size'] }} · version {{ $fileLabel['version'] }}@if ($fileLabel['submitted_at']) · submitted {{ $fileLabel['submitted_at'] }}@endif</p>
                    @if ($state === 'open')
                        <p class="mt-2 text-sm leading-6 text-catalyst-ink/75">You can replace this file until <time datetime="{{ $round->ends_at->toIso8601String() }}">{{ $round->ends_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</time>.</p>
                    @endif
                </section>
            @endif

            @if ($canUpload)
                <section class="border border-catalyst-grey/30 bg-white p-5 sm:p-6" aria-labelledby="upload-heading">
                    <h2 id="upload-heading" class="font-display text-xl font-semibold tracking-tight">{{ $submission ? 'Replace your file' : 'Upload your file' }}</h2>
                    <p class="mt-2 text-sm leading-6 text-catalyst-ink/75">One {{ strtolower(implode(', ', \App\Actions\Submissions\RequestSubmissionUpload::ALLOWED_FORMATS)) }} file, maximum 5 MB. Sending a new file replaces the previous one.</p>

                    <input x-ref="fileInput" class="sr-only" type="file" accept=".pdf,.ppt,.pptx,application/pdf,application/vnd.ms-powerpoint,application/vnd.openxmlformats-officedocument.presentationml.presentation" @change="upload($event.target.files[0]); $event.target.value = ''">
                    <div class="mt-5 grid min-h-52 place-items-center border border-dashed border-catalyst-primary/35 bg-catalyst-neutral p-6 text-center" @drop.prevent="upload($event.dataTransfer.files[0])" @dragover.prevent>
                        <div>
                            <svg class="mx-auto mb-4 size-7 text-catalyst-primary" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 16V4m0 0L7 9m5-5 5 5M4 16v4h16v-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>
                            <p class="font-medium text-catalyst-ink">Drop your file here</p>
                            <p class="mt-2 text-sm text-catalyst-ink/70">or choose a PDF, PPT, or PPTX file.</p>
                            <button class="mt-5 inline-flex min-h-11 items-center justify-center bg-catalyst-primary px-4 py-3 text-sm font-medium text-white focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-catalyst-primary disabled:opacity-60" type="button" @click="$refs.fileInput.click()" :disabled="uploading">Choose file</button>
                        </div>
                    </div>

                    <div class="mt-4 space-y-3" x-show="uploading" x-cloak>
                        <p class="break-all text-sm font-medium text-catalyst-ink" x-text="fileName"></p>
                        <div class="h-1.5 overflow-hidden bg-catalyst-grey/20" role="progressbar" aria-valuemin="0" aria-valuemax="100" :aria-valuenow="progress">
                            <div class="h-full bg-catalyst-primary transition-[width] duration-300 motion-reduce:transition-none" :style="`width: ${progress}%`"></div>
                        </div>
                        <p class="text-sm text-catalyst-ink/70" x-text="`Uploading ${progress}%`"></p>
                    </div>

                    <div class="mt-4" aria-live="polite" x-show="error" x-cloak>
                        <p class="border border-status-error/30 bg-status-error/5 p-3 text-sm leading-6 text-catalyst-ink"><span class="font-medium text-status-error-ink">Upload issue.</span> <span x-text="error"></span></p>
                    </div>
                </section>
            @endif

            <section class="border border-catalyst-grey/30 bg-white p-5 sm:p-6" aria-labelledby="submission-requirements-heading">
                <h2 id="submission-requirements-heading" class="font-display text-xl font-semibold tracking-tight">Submission requirements</h2>
                <dl class="mt-6 grid gap-5 text-sm sm:grid-cols-3">
                    <div><dt class="text-catalyst-muted">Formats</dt><dd class="mt-1 font-medium">PDF, PPT, PPTX</dd></div>
                    <div><dt class="text-catalyst-muted">File size</dt><dd class="mt-1 font-medium">5 MB per file</dd></div>
                    <div>
                        <dt class="text-catalyst-muted">Deadline</dt>
                        <dd class="mt-1 font-medium">
                            @if ($round->ends_at)
                                <time datetime="{{ $round->ends_at->toIso8601String() }}">{{ $round->ends_at->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</time>
                            @else
                                Not announced
                            @endif
                        </dd>
                    </div>
                </dl>
            </section>

            @if ($feedback)
                <p class="border border-status-success/30 bg-status-success/5 p-4 text-sm leading-6 text-catalyst-ink" aria-live="polite">{{ $feedback }}</p>
            @endif
        </div>
    </x-ui.container>
</div>
