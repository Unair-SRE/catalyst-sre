<?php

namespace App\Livewire\Dashboard;

use App\Support\Dashboard\DashboardOverviewState;
use Carbon\CarbonImmutable;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class Overview extends Component
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
        $now = CarbonImmutable::now('Asia/Jakarta');

        return view('livewire.dashboard.overview', [
            'state' => app(DashboardOverviewState::class)->for($this->scenario, $now),
            'scenarios' => DashboardOverviewState::scenarios(),
        ]);
    }

    private function normaliseScenario(string $scenario): string
    {
        return array_key_exists($scenario, DashboardOverviewState::scenarios()) ? $scenario : 'first_time_user';
    }
}
