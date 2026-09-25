<?php

namespace App\Filament\Resources\SummitOrders\Pages;

use App\Filament\Resources\SummitOrders\SummitOrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSummitOrders extends ListRecords
{
    protected static string $resource = SummitOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
