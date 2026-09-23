<?php

namespace App\Livewire\Dashboard;

use App\Support\Dashboard\DashboardSummitPassState;
use Carbon\CarbonImmutable;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class SummitPass extends Component
{
    #[Url(as: 'scenario', except: 'no_pass')]
    public string $scenario = 'no_pass';

    public string $status = 'NO_PASS';

    /** @var array<string, string> */
    public array $attendee = [];

    /** @var array<string, mixed> */
    public array $payment = [];

    /** @var array<string, string>|null */
    public ?array $ticket = null;

    /** @var array<int, array<string, string>> */
    public array $history = [];

    public ?string $submittedAt = null;

    public ?string $checkedInAt = null;

    public ?string $rejectionReason = null;

    public ?string $feedback = null;

    public bool $editingPayment = false;

    public bool $showConfirm = false;

    public function mount(): void
    {
        $this->loadScenario();
    }

    public function updatedScenario(): void
    {
        $this->loadScenario();
    }

    public function startPurchase(): void
    {
        $this->scenario = 'purchase';
        $this->loadScenario();
    }

    public function updatePayment(): void
    {
        if ($this->status !== 'REJECTED') {
            return;
        }

        $this->editingPayment = true;
        $this->feedback = null;
    }

    /**
     * Receives payment-proof metadata only. The file bytes never leave the browser in this prototype.
     *
     * @param  array<string, mixed>  $proof
     */
    public function selectProof(array $proof): void
    {
        if (! $this->formEditable()) {
            return;
        }

        $type = (string) ($proof['type'] ?? 'application/octet-stream');
        $size = (int) ($proof['size_bytes'] ?? 0);

        if (! in_array($type, ['image/jpeg', 'image/png'], true)) {
            $this->addError('payment.proof', 'Payment proof must be a JPG, JPEG, or PNG image.');

            return;
        }

        if ($size > 10 * 1024 * 1024) {
            $this->addError('payment.proof', 'Payment proof may not be larger than 10 MB.');

            return;
        }

        $this->resetErrorBag('payment.proof');
        $this->payment['proof'] = [
            'name' => (string) ($proof['name'] ?? 'payment-proof'),
            'type' => $type,
            'size_bytes' => $size,
            'size_label' => number_format($size / 1024 / 1024, 1).' MB',
        ];
    }

    public function removeProof(): void
    {
        if ($this->formEditable()) {
            $this->payment['proof'] = null;
        }
    }

    public function openConfirmation(): void
    {
        if (! $this->formEditable()) {
            return;
        }

        $this->validate($this->rules());
        $this->showConfirm = true;
    }

    public function cancelConfirmation(): void
    {
        $this->showConfirm = false;
    }

    public function submitPurchase(): void
    {
        if (! $this->formEditable()) {
            return;
        }

        $this->validate($this->rules());
        $resubmission = $this->status === 'REJECTED';
        $now = CarbonImmutable::now('Asia/Jakarta');

        $this->status = 'WAITING_VERIFICATION';
        $this->scenario = 'payment_waiting';
        $this->submittedAt = $now->format('Y-m-d H:i:s');
        $this->history[] = app(DashboardSummitPassState::class)->history(
            $resubmission ? 'Payment resubmitted' : 'Payment submitted',
            $this->submittedAt,
            'Payment is waiting for Catalyst review.',
        );
        $this->feedback = 'Summit Pass purchase submitted for payment review.';
        $this->editingPayment = false;
        $this->showConfirm = false;
    }

    public function render(): View
    {
        return view('livewire.dashboard.summit-pass', [
            'state' => $this->state(),
            'scenarios' => DashboardSummitPassState::scenarios(),
            'form_editable' => $this->formEditable(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function state(): array
    {
        $provider = app(DashboardSummitPassState::class);
        $state = $provider->for($this->scenario);

        $state['status'] = $this->status;
        $state['status_display'] = $provider->status($this->status);
        $state['attendee'] = $this->attendee;
        $state['payment'] = $this->payment;
        $state['ticket'] = $this->ticket;
        $state['history'] = $this->history;
        $state['submitted_at'] = $this->submittedAt;
        $state['submitted_label'] = $this->submittedAt ? $this->dateTimeLabel($this->submittedAt) : null;
        $state['checked_in_at'] = $this->checkedInAt;
        $state['checked_in_label'] = $this->checkedInAt ? $this->dateTimeLabel($this->checkedInAt) : null;
        $state['rejection_reason'] = $this->rejectionReason;

        return $state;
    }

    private function loadScenario(): void
    {
        $this->scenario = array_key_exists($this->scenario, DashboardSummitPassState::scenarios()) ? $this->scenario : 'no_pass';
        $state = app(DashboardSummitPassState::class)->for($this->scenario);

        $this->status = $state['status'];
        $this->attendee = $state['attendee'];
        $this->payment = $state['payment'];
        $this->ticket = $state['ticket'];
        $this->history = $state['history'];
        $this->submittedAt = $state['submitted_at'];
        $this->checkedInAt = $state['checked_in_at'];
        $this->rejectionReason = $state['rejection_reason'];
        $this->editingPayment = false;
        $this->feedback = null;
        $this->showConfirm = false;
    }

    private function formEditable(): bool
    {
        return $this->status === 'PURCHASE' || ($this->status === 'REJECTED' && $this->editingPayment);
    }

    /**
     * @return array<string, string>
     */
    private function rules(): array
    {
        return [
            'attendee.name' => 'required|string|max:120',
            'attendee.email' => 'required|email|max:160',
            'attendee.whatsapp' => 'required|string|max:40',
            'attendee.institution' => 'required|string|max:160',
            'payment.sender_name' => 'required|string|max:120',
            'payment.date' => 'required|date',
            'payment.time' => 'required|date_format:H:i',
            'payment.proof' => 'required|array',
            'payment.proof.type' => 'required|in:image/jpeg,image/png',
            'payment.proof.size_bytes' => 'required|integer|max:10485760',
        ];
    }

    private function dateTimeLabel(string $value): string
    {
        return CarbonImmutable::parse($value, 'Asia/Jakarta')->format('d F Y · H:i').' WIB';
    }
}
