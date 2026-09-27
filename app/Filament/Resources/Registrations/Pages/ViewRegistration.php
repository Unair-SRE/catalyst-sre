<?php

namespace App\Filament\Resources\Registrations\Pages;

use App\Filament\Resources\Registrations\RegistrationResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewRegistration extends ViewRecord
{
    protected static string $resource = RegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('openFolder')->label('Open team documents')
                ->icon(Heroicon::OutlinedFolderOpen)
                ->url(fn (): ?string => $this->record->team->documents_drive_url)
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->record->team->hasDocumentsFolder()),
        ];
    }
}
