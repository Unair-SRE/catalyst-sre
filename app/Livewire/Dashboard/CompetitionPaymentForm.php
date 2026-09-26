<?php

namespace App\Livewire\Dashboard;

use App\Actions\Payments\ConfirmDrivePayment;
use App\Enums\CompetitionCode;
use App\Models\Payment;
use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class CompetitionPaymentForm extends Component
{
    #[Locked]
    public string $competition;

    public string $senderName = '';

    public bool $documentsConfirmed = false;

    public ?string $feedback = null;

    public function mount(string $competition): void
    {
        abort_if(CompetitionCode::tryFrom(strtoupper($competition)) === null, 404);
        $this->competition = strtolower($competition);
        $this->senderName = $this->payment()->sender_name ?? Auth::user()->name;
    }

    public function submit(ConfirmDrivePayment $action): void
    {
        $this->validate([
            'senderName' => ['required', 'string', 'max:120'],
            'documentsConfirmed' => ['accepted'],
        ]);
        try {
            $action->handle(Auth::user(), $this->payment(), $this->senderName, $this->documentsConfirmed);
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }

            return;
        }
        $this->resetValidation();
        $this->documentsConfirmed = false;
        $this->feedback = 'Documents submitted. The committee will review all KTM files and the competition payment proof.';
    }

    public function render(): View
    {
        return view('livewire.dashboard.competition-payment-form', [
            'payment' => $this->payment()->load(['registration.competition', 'registration.team']),
            'setting' => PaymentSetting::query()->where('is_active', true)->latest('id')->first(),
        ]);
    }

    private function payment(): Payment
    {
        return Payment::query()
            ->whereHas('registration', fn ($query) => $query
                ->whereHas('team', fn ($team) => $team->where('captain_id', Auth::id()))
                ->whereHas('competition', fn ($competition) => $competition->where('code', strtoupper($this->competition))))
            ->firstOrFail();
    }
}
