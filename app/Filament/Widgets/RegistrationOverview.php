<?php

namespace App\Filament\Widgets;

use App\Enums\PaymentStatus;
use App\Enums\RegistrationStatus;
use App\Enums\SummitOrderStatus;
use App\Enums\SummitTicketStatus;
use App\Enums\UserRole;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\SummitOrder;
use App\Models\SummitTicket;
use App\Models\Team;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RegistrationOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected ?string $heading = 'Operational summary';

    protected ?string $description = 'Current registration, payment, and Summit activity.';

    protected int | array | null $columns = [
        'md' => 2,
        'xl' => 4,
    ];

    protected function getStats(): array
    {
        $competitionPaymentsWaiting = Payment::query()
            ->where('status', PaymentStatus::WaitingVerification)
            ->count();
        $summitPaymentsWaiting = SummitOrder::query()
            ->where('payment_status', SummitOrderStatus::WaitingVerification)
            ->count();
        $verifiedPayments = Payment::query()->where('status', PaymentStatus::Verified)->count()
            + SummitOrder::query()->where('payment_status', SummitOrderStatus::Verified)->count();

        return [
            Stat::make('Participants', number_format(User::query()->where('role', UserRole::Participant->value)->count()))
                ->description('Registered accounts'),
            Stat::make('Teams', number_format(Team::query()->count()))
                ->description('Competition teams'),
            Stat::make('Registrations', number_format(Registration::query()->count()))
                ->description('All competition entries'),
            Stat::make('Registrations to Process', number_format(Registration::query()
                ->where('status', RegistrationStatus::Pending)->count()))
                ->description('Pending registration review')
                ->descriptionColor('warning'),
            Stat::make('Payments to Verify', number_format($competitionPaymentsWaiting + $summitPaymentsWaiting))
                ->description('Competition and Summit payments')
                ->descriptionColor('warning'),
            Stat::make('Verified Payments', number_format($verifiedPayments))
                ->description('Competition and Summit payments')
                ->descriptionColor('success'),
            Stat::make('Active Summit Tickets', number_format(SummitTicket::query()
                ->where('status', SummitTicketStatus::Active)->count()))
                ->description('Ready for check-in'),
            Stat::make('Summit Check-ins', number_format(SummitTicket::query()
                ->where('status', SummitTicketStatus::Used)->count()))
                ->description('Tickets already used'),
        ];
    }
}
