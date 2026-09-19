<?php

namespace App\Filament\Resources\Teams;

use App\Filament\Resources\Teams\Pages\EditTeam;
use App\Filament\Resources\Teams\Pages\ListTeams;
use App\Models\Team;
use BackedEnum;
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
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('institution')->searchable(),
                TextColumn::make('captain.name')->label('Captain')->searchable(),
                TextColumn::make('captain.email')->label('Captain email')->searchable(),
                TextColumn::make('members_count')->counts('members')->label('Members'),
                IconColumn::make('captain_ktm')
                    ->label('Captain KTM')
                    ->state(fn (Team $record): bool => $record->captain->hasCompleteKtm())
                    ->boolean(),
                TextColumn::make('captain.ktm_file_id')
                    ->label('Captain ImageKit file ID')
                    ->placeholder('Missing')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('captain.ktm_url')
                    ->label('Captain KTM URL')
                    ->placeholder('Missing')
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('locked_at')->label('Locked')->boolean(),
            ])
            ->recordActions([
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
}
