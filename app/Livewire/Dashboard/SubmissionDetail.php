<?php

namespace App\Livewire\Dashboard;

use App\Support\Dashboard\DashboardSubmissionState;
use Carbon\CarbonImmutable;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class SubmissionDetail extends Component
{
    public string $competition;

    public ?string $stage = null;

    #[Url(as: 'scenario', except: 'open_empty')]
    public string $scenario = 'open_empty';

    /** @var array<int, array<string, mixed>> */
    public array $files = [];

    /** @var array<int, array<string, string>> */
    public array $history = [];

    /** @var array<int, string> */
    public array $uploadErrors = [];

    public string $stageState = 'open';

    public string $submissionState = 'not_submitted';

    public string $submissionAccess = 'available';

    public ?string $activeSubmissionAt = null;

    public ?string $revisionNote = null;

    public ?string $feedback = null;

    public bool $showSubmitConfirm = false;

    public bool $showUnsubmitConfirm = false;

    public bool $showHistory = false;

    public function mount(string $competition, ?string $stage = null): void
    {
        $this->competition = $competition;
        $this->stage = $stage;
        $this->loadScenario();
    }

    public function updatedScenario(): void
    {
        $this->loadScenario();
    }

    /**
     * Receives only browser-side file metadata. No file bytes are uploaded or persisted in this prototype.
     *
     * @param  array<int, array<string, mixed>>  $entries
     */
    public function addFiles(array $entries): void
    {
        $state = $this->state();

        if (! $this->canEdit($state)) {
            return;
        }

        $this->uploadErrors = [];
        $requirements = $state['requirements'];

        foreach ($entries as $entry) {
            $name = (string) ($entry['name'] ?? 'Untitled file');
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $size = (int) ($entry['size_bytes'] ?? 0);

            if (! in_array($extension, $requirements['allowed_extensions'], true)) {
                $this->uploadErrors[] = "Unsupported format: {$name}. Upload a {$requirements['allowed_label']} file.";

                continue;
            }

            if ($size > $requirements['max_file_bytes']) {
                $this->uploadErrors[] = "File too large: {$name} exceeds the {$requirements['max_file_label']} limit. Please compress it and try again.";

                continue;
            }

            if (count($this->files) >= $requirements['max_files']) {
                $this->uploadErrors[] = "Maximum files reached. You can upload up to {$requirements['max_files']} files for this stage.";

                break;
            }

            $this->files[] = [
                'id' => (string) ($entry['id'] ?? Str::uuid()),
                'name' => $name,
                'type' => (string) ($entry['type'] ?? 'application/octet-stream'),
                'size_bytes' => $size,
                'size_label' => $this->fileSize($size),
                'state' => 'uploading',
                'progress' => 10,
                'error' => null,
            ];
        }
    }

    public function finishUpload(string $id): void
    {
        foreach ($this->files as $index => $file) {
            if ($file['id'] !== $id || $file['state'] !== 'uploading') {
                continue;
            }

            $this->files[$index]['state'] = 'uploaded';
            $this->files[$index]['progress'] = 100;
            $this->appendHistory('Uploaded', "{$file['name']} · New file");
        }
    }

    public function retryUpload(string $id): void
    {
        foreach ($this->files as $index => $file) {
            if ($file['id'] !== $id || $file['state'] !== 'failed') {
                continue;
            }

            $this->files[$index]['state'] = 'uploading';
            $this->files[$index]['progress'] = 10;
            $this->files[$index]['error'] = null;
            $this->dispatch('submission-upload-ready', id: $id);
        }
    }

    public function removeFile(string $id): void
    {
        $file = collect($this->files)->firstWhere('id', $id);

        if ($file === null || ! $this->canEdit($this->state())) {
            return;
        }

        $this->files = array_values(array_filter($this->files, fn (array $item): bool => $item['id'] !== $id));
        $this->appendHistory('File removed', $file['name']);
    }

    public function openSubmitConfirmation(): void
    {
        if (! $this->canEdit($this->state())) {
            return;
        }

        if (collect($this->files)->where('state', 'uploaded')->isEmpty()) {
            $this->feedback = 'Add at least one uploaded file before submitting your entry.';

            return;
        }

        $this->feedback = null;
        $this->showSubmitConfirm = true;
    }

    public function cancelSubmitConfirmation(): void
    {
        $this->showSubmitConfirm = false;
    }

    public function submitEntry(): void
    {
        if (! $this->canEdit($this->state()) || collect($this->files)->where('state', 'uploaded')->isEmpty()) {
            return;
        }

        $now = CarbonImmutable::now('Asia/Jakarta');
        $deadline = CarbonImmutable::parse($this->state()['stage']['deadline_at'], 'Asia/Jakarta');
        $wasSubmitted = collect($this->history)->contains(fn (array $event): bool => in_array($event['event'], ['Submitted', 'Resubmitted', 'Resubmitted late'], true));

        $this->activeSubmissionAt = $now->format('Y-m-d H:i:s');
        $this->submissionState = $now->isAfter($deadline) ? 'late' : 'submitted';
        $this->appendHistory($wasSubmitted ? 'Resubmitted' : 'Submitted', count($this->files).' files · Active version');
        $this->feedback = 'Submission received. Your latest active submission timestamp is recorded below.';
        $this->showSubmitConfirm = false;
    }

    public function openUnsubmitConfirmation(): void
    {
        if ($this->canUnsubmit($this->state())) {
            $this->showUnsubmitConfirm = true;
        }
    }

    public function cancelUnsubmitConfirmation(): void
    {
        $this->showUnsubmitConfirm = false;
    }

    public function unsubmitEntry(): void
    {
        if (! $this->canUnsubmit($this->state())) {
            return;
        }

        $this->submissionState = 'editable';
        $this->activeSubmissionAt = null;
        $this->appendHistory('Unsubmitted', 'The previous active version remains in history.');
        $this->feedback = 'Your entry is editable again. Update the files, then submit a new active version.';
        $this->showUnsubmitConfirm = false;
    }

    public function toggleHistory(): void
    {
        $this->showHistory = ! $this->showHistory;
    }

    public function render(): View
    {
        return view('livewire.dashboard.submission-detail', [
            'state' => $this->state(),
            'scenarios' => DashboardSubmissionState::detailScenarios(),
            'can_edit' => $this->canEdit($this->state()),
            'can_unsubmit' => $this->canUnsubmit($this->state()),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function state(): array
    {
        $provider = app(DashboardSubmissionState::class);
        $state = $provider->detail($this->competition, $this->stage, $this->scenario);

        abort_if($state === null, 404);

        $state['files'] = $this->files;
        $state['history'] = $this->history;
        $state['stage_state'] = $this->stageState;
        $state['stage_lifecycle'] = $provider->stageLifecycle($this->stageState);
        $state['submission_state'] = $this->submissionState;
        $state['submission'] = $provider->submissionStatus($this->submissionState);
        $state['submission_access'] = $this->submissionAccess;
        $state['access'] = $provider->accessStatus($this->submissionAccess);
        $state['active_submission_at'] = $this->activeSubmissionAt;
        $state['active_submission_label'] = $this->activeSubmissionAt ? $this->dateTimeLabel($this->activeSubmissionAt) : null;
        $state['lateness'] = $this->activeSubmissionAt
            ? $this->lateness($this->activeSubmissionAt, $state['stage']['deadline_at'])
            : null;
        $state['revision_note'] = $this->revisionNote;

        return $state;
    }

    private function loadScenario(): void
    {
        $this->scenario = array_key_exists($this->scenario, DashboardSubmissionState::detailScenarios()) ? $this->scenario : 'open_empty';
        $state = app(DashboardSubmissionState::class)->detail($this->competition, $this->stage, $this->scenario);

        abort_if($state === null, 404);

        $this->files = $state['files'];
        $this->history = $state['history'];
        $this->stageState = $state['stage_state'];
        $this->submissionState = $state['submission_state'];
        $this->submissionAccess = $state['submission_access'];
        $this->activeSubmissionAt = $state['active_submission_at'];
        $this->revisionNote = $state['revision_note'];
        $this->uploadErrors = [];
        $this->feedback = null;
        $this->showSubmitConfirm = false;
        $this->showUnsubmitConfirm = false;
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private function canEdit(array $state): bool
    {
        return $state['submission_access'] === 'available'
            && in_array($state['stage_state'], ['open', 'late_window'], true)
            && in_array($state['submission_state'], ['not_submitted', 'editable', 'revision_required'], true);
    }

    /**
     * @param  array<string, mixed>  $state
     */
    private function canUnsubmit(array $state): bool
    {
        return $state['submission_access'] === 'available'
            && in_array($state['stage_state'], ['open', 'late_window'], true)
            && in_array($state['submission_state'], ['submitted', 'late'], true);
    }

    private function appendHistory(string $event, string $detail): void
    {
        $now = CarbonImmutable::now('Asia/Jakarta');
        $timestamp = $now->format('Y-m-d H:i:s');

        $this->history[] = [
            'event' => $event,
            'timestamp' => $timestamp,
            'timestamp_label' => $this->dateTimeLabel($timestamp),
            'detail' => $detail,
        ];
    }

    private function dateTimeLabel(string $value): string
    {
        return CarbonImmutable::parse($value, 'Asia/Jakarta')->format('d F Y · H:i').' WIB';
    }

    private function fileSize(int $bytes): string
    {
        return number_format($bytes / 1024 / 1024, 1).' MB';
    }

    private function lateness(string $submittedAt, string $deadlineAt): ?string
    {
        $seconds = CarbonImmutable::parse($submittedAt, 'Asia/Jakarta')->getTimestamp() - CarbonImmutable::parse($deadlineAt, 'Asia/Jakarta')->getTimestamp();

        if ($seconds <= 0) {
            return null;
        }

        $minutes = intdiv($seconds, 60);

        return $minutes < 60
            ? "{$minutes} minutes late"
            : intdiv($minutes, 60).'h '.($minutes % 60).'m late';
    }
}
