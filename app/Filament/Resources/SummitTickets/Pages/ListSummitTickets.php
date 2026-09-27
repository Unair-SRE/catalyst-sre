<?php

namespace App\Filament\Resources\SummitTickets\Pages;

use App\Filament\Resources\SummitTickets\SummitTicketResource;
use Filament\Resources\Pages\ListRecords;

class ListSummitTickets extends ListRecords
{
    protected static string $resource = SummitTicketResource::class;
}
