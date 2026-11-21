<?php

namespace App\Filament\Resources\QualificationResults\Pages;

use App\Filament\Resources\QualificationResults\QualificationResultResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQualificationResults extends ListRecords
{
    protected static string $resource = QualificationResultResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
