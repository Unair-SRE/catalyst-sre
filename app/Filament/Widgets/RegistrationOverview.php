<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Models\Competition;
use App\Models\Payment;
use App\Models\Team;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RegistrationOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $competitionStats = Competition::query()
            ->withCount('registrations')
            ->orderBy('code')
            ->get()
            ->map(fn (Competition $competition): Stat => Stat::make(
                $competition->code->value.' Registrations',
                number_format($competition->registrations_count),
            )->description($competition->name))
            ->all();

        return [
            Stat::make('Total Users', number_format(User::query()->count())),
            Stat::make('Total Teams', number_format(Team::query()->count())),
            ...$competitionStats,
            Stat::make(
                'Payments Awaiting Verification',
                number_format(Payment::query()->where('status', PaymentStatus::WaitingVerification)->count()),
            ),
        ];
    }
}
