<?php

namespace App\Livewire\Dashboard;

use App\Enums\CompetitionCode;
use App\Models\Competition;
use App\Support\Dashboard\DashboardRegistrationState;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class RegistrationDetail extends Component
{
    public string $competition;

    #[Url(as: 'scenario', except: 'draft')]
    public string $scenario = 'draft';

    public array $form = [];

    public bool $showConfirm = false;

    public ?string $paymentUrl = null;

    public function mount(string $competition): void
    {
        $this->competition = $competition;
        $this->loadScenario();
        $this->paymentUrl = $this->resolvePaymentUrl();
    }

    public function updatedScenario(): void
    {
        $this->loadScenario();
    }

    public function openConfirmation(): void
    {
        $state = $this->state();

        if (! $state['form_editable']) {
            return;
        }

        $this->validate($this->rules($state));
        $this->showConfirm = true;
    }

    public function cancelConfirmation(): void
    {
        $this->showConfirm = false;
    }

    public function submitRegistration(): void
    {
        $this->validate($this->rules($this->state()));
        $this->scenario = 'submitted';
        $this->showConfirm = false;
        $this->loadScenario();
    }

    public function updateRegistration(): void
    {
        $this->scenario = 'draft';
        $this->loadScenario();
    }

    public function render(): View
    {
        return view('livewire.dashboard.registration-detail', [
            'state' => $this->state(),
            'scenarios' => DashboardRegistrationState::detailScenarios(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function state(): array
    {
        $state = app(DashboardRegistrationState::class)->detail($this->competition, $this->scenario);

        abort_if($state === null, 404);

        $state['form'] = $this->form;

        return $state;
    }

    private function loadScenario(): void
    {
        $this->scenario = array_key_exists($this->scenario, DashboardRegistrationState::detailScenarios()) ? $this->scenario : 'draft';
        $state = app(DashboardRegistrationState::class)->detail($this->competition, $this->scenario);

        abort_if($state === null, 404);

        $this->form = $state['form'];
    }

    /**
     * @param  array<string, mixed>  $state
     * @return array<string, string>
     */
    private function rules(array $state): array
    {
        $rules = [
            'form.team_name' => 'required|string|max:120',
            'form.institution' => 'required|string|max:160',
            'form.eligible' => 'accepted',
            'form.members.0.legal_name' => 'required|string|max:120',
            'form.members.0.email' => 'required|email',
            'form.members.0.whatsapp' => 'required|string|max:40',
            'form.members.1.legal_name' => 'required|string|max:120',
            'form.members.1.email' => 'required|email',
            'form.members.1.whatsapp' => 'required|string|max:40',
            'form.drive_url' => 'required|url',
        ];

        if ($state['payment_required']) {
            $rules = [
                ...$rules,
                'form.payment_sender' => 'required|string|max:120',
                'form.payment_date' => 'required|date',
                'form.payment_time' => 'required|date_format:H:i',
            ];
        }

        return $rules;
    }

    private function resolvePaymentUrl(): ?string
    {
        $code = CompetitionCode::tryFrom(strtoupper($this->competition));
        $team = Auth::user()?->captainedTeam;

        if ($code === null || $team === null) {
            return null;
        }

        $competitionId = Competition::query()->where('code', $code)->value('id');

        if ($competitionId === null || ! $team->registrations()->where('competition_id', $competitionId)->exists()) {
            return null;
        }

        return route('dashboard.registration.payment', $this->competition);
    }
}
