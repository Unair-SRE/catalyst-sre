<?php

namespace App\Livewire\Dashboard;

use App\Actions\Payments\SubmitCompetitionPayment;
use App\Enums\CompetitionCode;
use App\Models\Competition;
use App\Models\Payment;
use App\Models\PaymentSetting;
use App\Models\Registration;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Throwable;

class CompetitionPaymentForm extends Component
{
    use WithFileUploads;

    public string $competition;

    public int $paymentId;

    public string $senderName = '';

    public ?TemporaryUploadedFile $proof = null;

    public ?string $feedback = null;

    public function mount(string $competition): void
    {
        $this->competition = $competition;
        $registration = $this->registration();
        $payment = $registration->payment()->firstOrCreate();
        $this->paymentId = $payment->id;
        $this->senderName = $payment->sender_name ?? Auth::user()->name;
    }

    public function submit(SubmitCompetitionPayment $submitPayment): void
    {
        $this->validate([
            'senderName' => ['required', 'string', 'max:120'],
            'proof' => ['required', 'file', 'mimes:jpg,jpeg,png', 'mimetypes:image/jpeg,image/png', 'max:2048'],
        ]);

        try {
            $submitPayment->handle(Auth::user(), $this->payment(), $this->senderName, $this->proof);
        } catch (Throwable $exception) {
            report($exception);
            $this->addError('proof', 'The payment proof could not be uploaded. Please try again.');

            return;
        }

        $this->proof = null;
        $this->feedback = 'Payment proof submitted and is waiting for verification.';
    }

    public function render(): View
    {
        return view('livewire.dashboard.competition-payment-form', [
            'payment' => $this->payment()->load('registration.competition'),
            'setting' => PaymentSetting::query()->where('is_active', true)->latest('id')->first(),
        ]);
    }

    private function registration(): Registration
    {
        $code = CompetitionCode::tryFrom(strtoupper($this->competition));
        abort_if($code === null, 404);

        $competition = Competition::query()->where('code', $code)->firstOrFail();
        $team = Auth::user()->captainedTeam;
        abort_if($team === null, 404);

        return Registration::query()
            ->where('team_id', $team->id)
            ->where('competition_id', $competition->id)
            ->firstOrFail();
    }

    private function payment(): Payment
    {
        return Payment::query()->findOrFail($this->paymentId);
    }
}
