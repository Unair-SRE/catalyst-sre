<?php

namespace App\Filament\Resources\TeamMembers;

use App\Filament\Resources\TeamMembers\Pages\EditTeamMember;
use App\Filament\Resources\TeamMembers\Pages\ListTeamMembers;
use App\Models\TeamMember;
use App\Rules\AvailableTeamEmail;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
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
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('ktm_preview')
                    ->label('KTM')
                    ->state(fn (TeamMember $record): ?string => static::ktmPreviewUrl($record))
                    ->square()
                    ->imageSize(48)
                    ->checkFileExistence(false),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('whatsapp')->searchable(),
                TextColumn::make('team.name')->label('Team')->searchable()->sortable(),
            ])
            ->recordActions([
                Action::make('viewKtm')
                    ->label('View KTM')
                    ->icon(Heroicon::OutlinedIdentification)
                    ->modalHeading(fn (TeamMember $record): string => $record->name.' — KTM')
                    ->modalContent(fn (TeamMember $record) => view('ktm-details', [
                        'personName' => $record->name,
                        'teamName' => $record->team->name,
                        'fileId' => $record->ktm_file_id,
                        'imageUrl' => static::ktmPreviewUrl($record),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->visible(fn (TeamMember $record): bool => $record->hasCompleteKtm()),
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

    private static function ktmPreviewUrl(TeamMember $member): ?string
    {
        if (! $member->hasCompleteKtm()) {
            return null;
        }

        if (str_starts_with($member->ktm_file_id, 'seed-')) {
            return asset('images/brand/catalyst-mark.png');
        }

        return route('dashboard.private-files.team-member-ktm', $member);
    }
}
