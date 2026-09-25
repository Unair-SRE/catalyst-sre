<?php

namespace App\Filament\Resources\SummitTickets\Pages;

use App\Filament\Resources\SummitTickets\SummitTicketResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSummitTickets extends ListRecords
{
    protected static string $resource = SummitTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
