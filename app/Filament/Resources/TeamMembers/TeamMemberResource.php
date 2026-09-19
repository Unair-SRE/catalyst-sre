<?php

namespace App\Filament\Resources\TeamMembers;

use App\Filament\Resources\TeamMembers\Pages\EditTeamMember;
use App\Filament\Resources\TeamMembers\Pages\ListTeamMembers;
use App\Models\TeamMember;
use App\Rules\AvailableTeamEmail;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(120),
            TextInput::make('email')
                ->required()
                ->email()
                ->maxLength(255)
                ->rules(fn (?TeamMember $record): array => [new AvailableTeamEmail($record?->id)]),
            TextInput::make('whatsapp')->required()->maxLength(20),
            TextInput::make('ktm_url')->label('KTM URL')->required()->url(),
            TextInput::make('ktm_file_id')->label('ImageKit file ID')->required()->maxLength(255),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('whatsapp')->searchable(),
                TextColumn::make('team.name')->label('Team')->searchable()->sortable(),
                TextColumn::make('ktm_file_id')->label('ImageKit file ID')->toggleable(),
                TextColumn::make('ktm_url')
                    ->label('KTM URL')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeamMembers::route('/'),
            'edit' => EditTeamMember::route('/{record}/edit'),
        ];
    }
}
