<?php

namespace App\Filament\Resources\SummitTickets\Pages;

use App\Actions\Summit\CheckInTicket;
use App\Enums\SummitTicketStatus;
use App\Filament\Resources\SummitTickets\SummitTicketResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class ViewSummitTicket extends ViewRecord
{
    protected static string $resource = SummitTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('download')->label('Download ticket PDF')
                ->icon(Heroicon::OutlinedTicket)
                ->url(fn (): string => route('dashboard.summit-ticket.download', $this->record))
                ->openUrlInNewTab()
                ->visible(fn (): bool => in_array($this->record->status, [SummitTicketStatus::Active, SummitTicketStatus::Used], true)),
            Action::make('checkIn')->label('Check in')->color('success')
                ->requiresConfirmation()
                ->modalDescription(fn (): string => "Use ticket {$this->record->ticket_code} for {$this->record->holder_name}? This cannot be undone.")
                ->visible(fn (): bool => $this->record->status === SummitTicketStatus::Active)
                ->action(fn () => app(CheckInTicket::class)->handle(Auth::user(), $this->record)),
        ];
    }
}
