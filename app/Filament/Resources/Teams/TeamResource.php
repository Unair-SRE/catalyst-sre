<?php

namespace App\Filament\Resources\Teams;

use App\Filament\Resources\Teams\Pages\EditTeam;
use App\Filament\Resources\Teams\Pages\ListTeams;
use App\Models\Team;
use App\Rules\GoogleDriveFolder;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(120),
            TextInput::make('institution')->required()->maxLength(160),
            TextInput::make('documents_drive_url')->label('Team Google Drive folder')->url()
                ->maxLength(2048)->rules([new GoogleDriveFolder])
                ->helperText('Changing the folder sends previously submitted Drive payments back for review.'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('institution')->searchable(),
            TextColumn::make('captain.name')->label('Captain')->searchable(),
            TextColumn::make('captain.email')->label('Captain email'),
            TextColumn::make('members_count')->counts('members')->label('Additional members'),
            IconColumn::make('locked_at')->label('Locked')->boolean(),
        ])->recordActions([
            Action::make('openFolder')->label('Open team folder')
                ->url(fn (Team $record): string => route('dashboard.team.documents', $record))
                ->openUrlInNewTab()->visible(fn (Team $record): bool => $record->hasDocumentsFolder()),
            EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListTeams::route('/'), 'edit' => EditTeam::route('/{record}/edit')];
    }
}
