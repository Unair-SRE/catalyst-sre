<?php

namespace App\Livewire\Dashboard;

use App\Actions\Summit\ConfirmSummitDrivePayment;
use App\Actions\Summit\CreateSummitOrder;
use App\Models\PaymentSetting;
use App\Models\SummitOrder;
use App\Rules\GoogleDriveFolder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Component;

class SummitPass extends Component
{
    /** @var array<int, string> */
    public array $holderNames = [''];

    public string $senderName = '';

    public string $driveUrl = '';

    public bool $documentsConfirmed = false;

    public bool $showPurchaseForm = false;

    public ?string $feedback = null;

    public function mount(): void
    {
        $this->senderName = Auth::user()->name;
        $this->showPurchaseForm = ! SummitOrder::query()->where('user_id', Auth::id())->exists();
    }

    public function startPurchase(): void
    {
        $this->resetPurchaseForm();
        $this->showPurchaseForm = true;
        $this->feedback = null;
    }

    public function cancelPurchase(): void
    {
        $this->showPurchaseForm = false;
        $this->resetValidation();
    }

    public function addHolder(): void
    {
        $this->holderNames[] = '';
    }

    public function removeHolder(int $index): void
    {
        if (count($this->holderNames) <= 1 || ! array_key_exists($index, $this->holderNames)) {
            return;
        }

        unset($this->holderNames[$index]);
        $this->holderNames = array_values($this->holderNames);
    }

    public function submitPurchase(
        CreateSummitOrder $createOrder,
        ConfirmSummitDrivePayment $confirmPayment,
    ): void {
        $validated = $this->validate([
            'holderNames' => ['required', 'array', 'min:1'],
            'holderNames.*' => ['required', 'string', 'max:120'],
            'senderName' => ['required', 'string', 'max:120'],
            'driveUrl' => ['required', 'string', 'max:2048', new GoogleDriveFolder],
            'documentsConfirmed' => ['accepted'],
        ]);

        try {
            $order = DB::transaction(function () use ($createOrder, $confirmPayment, $validated): SummitOrder {
                $order = $createOrder->handle(Auth::user(), $validated['holderNames']);

                return $confirmPayment->handle(
                    Auth::user(),
                    $order,
                    $validated['senderName'],
                    $validated['driveUrl'],
                    $validated['documentsConfirmed'],
                );
            });
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                $this->addError($this->formField($field), $messages[0]);
            }

            return;
        }

        $this->resetPurchaseForm();
        $this->showPurchaseForm = false;
        $this->feedback = "Summit order #{$order->id} was submitted for payment review.";
    }

    public function render(): View
    {
        return view('livewire.dashboard.summit-pass', [
            'orders' => SummitOrder::query()
                ->where('user_id', Auth::id())
                ->with(['tickets', 'verifier'])
                ->latest()
                ->get(),
            'setting' => PaymentSetting::query()->where('is_active', true)->latest('id')->first(),
        ]);
    }

    private function resetPurchaseForm(): void
    {
        $this->holderNames = [''];
        $this->senderName = Auth::user()->name;
        $this->driveUrl = '';
        $this->documentsConfirmed = false;
        $this->resetValidation();
    }

    private function formField(string $field): string
    {
        return match ($field) {
            'holders' => 'holderNames',
            'sender_name' => 'senderName',
            'payment_drive_url' => 'driveUrl',
            'confirmed' => 'documentsConfirmed',
            'order' => 'holderNames',
            default => $field,
        };
    }
}
