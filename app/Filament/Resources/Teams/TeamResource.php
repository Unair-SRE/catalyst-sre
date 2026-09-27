<?php

namespace App\Filament\Resources\Teams;

use App\Filament\Resources\Teams\Pages\EditTeam;
use App\Filament\Resources\Teams\Pages\ListTeams;
use App\Filament\Resources\Teams\Pages\ViewTeam;
use App\Models\Team;
use App\Rules\GoogleDriveFolder;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|\UnitEnum|null $navigationGroup = 'Competition';

    protected static ?int $navigationSort = 2;

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

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Team information')->schema([
                TextEntry::make('name')->label('Team'),
                TextEntry::make('institution'),
                TextEntry::make('captain.name')->label('Captain'),
                TextEntry::make('captain.email')->label('Captain email')->copyable(),
                TextEntry::make('captain.whatsapp')->label('Captain WhatsApp')->copyable(),
                TextEntry::make('locked_at')->label('Locked at')->dateTime()->placeholder('Not locked'),
                TextEntry::make('created_at')->label('Created at')->dateTime(),
                TextEntry::make('updated_at')->label('Last updated')->dateTime(),
            ])->columns(2),
            Section::make('Additional members')->schema([
                RepeatableEntry::make('members')->label('')->schema([
                    TextEntry::make('name'),
                    TextEntry::make('email')->copyable(),
                    TextEntry::make('whatsapp')->label('WhatsApp')->copyable(),
                ])->columns(3)->contained(false),
            ]),
            Section::make('Registrations and payments')->schema([
                RepeatableEntry::make('registrations')->label('')->schema([
                    TextEntry::make('competition.name')->label('Competition'),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('payment.status')->label('Payment')->badge()->placeholder('Not submitted'),
                    TextEntry::make('created_at')->label('Registered at')->dateTime(),
                ])->columns(4)->contained(false),
            ]),
            Section::make('Documents')->schema([
                TextEntry::make('documents_drive_url')->label('Google Drive folder')
                    ->url(fn (Team $record): ?string => $record->documents_drive_url)
                    ->openUrlInNewTab()->placeholder('Not submitted'),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('registrations.competition.code')->label('Competition')->badge(),
            TextColumn::make('captain.name')->label('Captain')->searchable(),
            TextColumn::make('registrations.status')->label('Registration')->badge(),
            TextColumn::make('registrations.payment.status')->label('Payment')->badge()->placeholder('Not submitted'),
        ])->recordActions([
            ViewAction::make(),
            EditAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTeams::route('/'),
            'view' => ViewTeam::route('/{record}'),
            'edit' => EditTeam::route('/{record}/edit'),
        ];
    }
}
