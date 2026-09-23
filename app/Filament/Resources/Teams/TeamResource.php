<?php

namespace App\Filament\Resources\Teams;

use App\Filament\Resources\Teams\Pages\EditTeam;
use App\Filament\Resources\Teams\Pages\ListTeams;
use App\Models\Team;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
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
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('captain_ktm_preview')
                    ->label('Captain KTM')
                    ->state(fn (Team $record): ?string => static::captainKtmPreviewUrl($record))
                    ->square()
                    ->imageSize(48)
                    ->checkFileExistence(false),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('institution')->searchable(),
                TextColumn::make('captain.name')->label('Captain')->searchable(),
                TextColumn::make('captain.email')->label('Captain email')->searchable(),
                TextColumn::make('members_count')->counts('members')->label('Members'),
                IconColumn::make('locked_at')->label('Locked')->boolean(),
            ])
            ->recordActions([
                Action::make('viewCaptainKtm')
                    ->label('View captain KTM')
                    ->icon(Heroicon::OutlinedIdentification)
                    ->modalHeading(fn (Team $record): string => $record->captain->name.' — KTM')
                    ->modalContent(fn (Team $record) => view('ktm-details', [
                        'personName' => $record->captain->name,
                        'teamName' => $record->name,
                        'fileId' => $record->captain->ktm_file_id,
                        'imageUrl' => static::captainKtmPreviewUrl($record),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->visible(fn (Team $record): bool => $record->captain->hasCompleteKtm()),
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeams::route('/'),
            'edit' => EditTeam::route('/{record}/edit'),
        ];
    }

    private static function captainKtmPreviewUrl(Team $team): ?string
    {
        if (! $team->captain->hasCompleteKtm()) {
            return null;
        }

        if (str_starts_with($team->captain->ktm_file_id, 'seed-')) {
            return asset('images/brand/catalyst-mark.png');
        }

        return route('dashboard.private-files.captain-ktm', $team->captain);
    }
}
