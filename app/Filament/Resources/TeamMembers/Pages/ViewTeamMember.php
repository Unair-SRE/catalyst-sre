<?php

namespace App\Filament\Resources\TeamMembers\Pages;

use App\Filament\Resources\TeamMembers\TeamMemberResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;

class ViewTeamMember extends ViewRecord
{
    protected static string $resource = TeamMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('openFolder')->label('Open team documents')
                ->icon(Heroicon::OutlinedFolderOpen)
                ->url(fn (): ?string => $this->record->team->documents_drive_url)
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->record->team->hasDocumentsFolder()),
            EditAction::make()->label('Edit member'),
        ];
    }
}
