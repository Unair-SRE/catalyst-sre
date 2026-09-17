<?php

namespace App\Livewire\Dashboard;

use App\Support\Dashboard\DashboardRegistrationState;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class RegistrationIndex extends Component
{
    #[Url(as: 'scenario', except: 'first_time_user')]
    public string $scenario = 'first_time_user';

    public function mount(): void
    {
        $this->scenario = $this->normaliseScenario($this->scenario);
    }

    public function updatedScenario(): void
    {
        $this->scenario = $this->normaliseScenario($this->scenario);
    }

    public function render(): View
    {
        return view('livewire.dashboard.registration-index', [
            'competitions' => app(DashboardRegistrationState::class)->index($this->scenario),
            'scenarios' => DashboardRegistrationState::indexScenarios(),
        ]);
    }

    private function normaliseScenario(string $scenario): string
    {
        return array_key_exists($scenario, DashboardRegistrationState::indexScenarios()) ? $scenario : 'first_time_user';
    }
}
