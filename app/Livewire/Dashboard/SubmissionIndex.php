<?php

namespace App\Livewire\Dashboard;

use App\Support\Dashboard\DashboardSubmissionState;
use Illuminate\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class SubmissionIndex extends Component
{
    #[Url(as: 'scenario', except: 'no_registration')]
    public string $scenario = 'no_registration';

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
        return view('livewire.dashboard.submission-index', [
            'competitions' => app(DashboardSubmissionState::class)->index($this->scenario),
            'scenarios' => DashboardSubmissionState::indexScenarios(),
        ]);
    }

    private function normaliseScenario(string $scenario): string
    {
        return array_key_exists($scenario, DashboardSubmissionState::indexScenarios()) ? $scenario : 'no_registration';
    }
}
