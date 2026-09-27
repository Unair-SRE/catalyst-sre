<?php

namespace App\Filament\Resources\TeamMembers;

use App\Filament\Resources\TeamMembers\Pages\EditTeamMember;
use App\Filament\Resources\TeamMembers\Pages\ListTeamMembers;
use App\Filament\Resources\TeamMembers\Pages\ViewTeamMember;
use App\Models\TeamMember;
use App\Rules\AvailableTeamEmail;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamMemberResource extends Resource
{
    protected static ?string $model = TeamMember::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|\UnitEnum|null $navigationGroup = 'Competition';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(120),
            TextInput::make('email')->required()->email()->maxLength(255)
                ->rules(fn (?TeamMember $record): array => [new AvailableTeamEmail($record?->id)]),
            TextInput::make('whatsapp')->required()->maxLength(20),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('email')->searchable(),
            TextColumn::make('team.name')->label('Team')->searchable(),
        ])->recordActions([
            ViewAction::make(),
            EditAction::make(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Member information')->schema([
                TextEntry::make('name'),
                TextEntry::make('email')->copyable(),
                TextEntry::make('whatsapp')->label('WhatsApp')->copyable(),
                TextEntry::make('team.name')->label('Team'),
                TextEntry::make('team.institution')->label('Institution'),
                TextEntry::make('created_at')->label('Added at')->dateTime(),
            ])->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeamMembers::route('/'),
            'view' => ViewTeamMember::route('/{record}'),
            'edit' => EditTeamMember::route('/{record}/edit'),
        ];
    }
}
