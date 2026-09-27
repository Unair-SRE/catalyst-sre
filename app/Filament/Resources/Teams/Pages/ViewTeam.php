<?php

namespace App\Filament\Resources\Teams\Pages;

use App\Filament\Resources\Teams\TeamResource;
use App\Models\Team;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewTeam extends ViewRecord
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('openFolder')->label('Open Google Drive')
                ->icon(Heroicon::OutlinedFolderOpen)
                ->url(fn (): ?string => $this->record->documents_drive_url)
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->record instanceof Team && $this->record->hasDocumentsFolder()),
            EditAction::make()->label('Edit team'),
        ];
    }
}
