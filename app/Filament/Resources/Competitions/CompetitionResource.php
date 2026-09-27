<?php

namespace App\Filament\Resources\Competitions;

use App\Enums\CompetitionCode;
use App\Filament\Resources\Competitions\Pages\CreateCompetition;
use App\Filament\Resources\Competitions\Pages\EditCompetition;
use App\Filament\Resources\Competitions\Pages\ListCompetitions;
use App\Filament\Resources\Competitions\Pages\ViewCompetition;
use App\Models\Competition;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompetitionResource extends Resource
{
    protected static ?string $model = Competition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static string|\UnitEnum|null $navigationGroup = 'Competition';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('code')
                ->options(collect(CompetitionCode::cases())->mapWithKeys(
                    fn (CompetitionCode $code): array => [$code->value => $code->label()],
                )->all())
                ->required()
                ->unique(ignoreRecord: true),
            TextInput::make('name')->required()->maxLength(120),
            Textarea::make('description')->columnSpanFull(),
            TextInput::make('registration_fee')
                ->required()
                ->numeric()
                ->minValue(0)
                ->prefix('IDR'),
            Toggle::make('registration_open')->label('Registration open'),
            DateTimePicker::make('registration_start_at')->label('Starts at'),
            DateTimePicker::make('registration_end_at')
                ->label('Closes at')
                ->after('registration_start_at'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->badge()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('registration_fee')->money('IDR')->sortable(),
                IconColumn::make('registration_open')->label('Open')->boolean(),
                TextColumn::make('registrations_count')->counts('registrations')->label('Registrations'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Competition information')->schema([
                TextEntry::make('code')->badge(),
                TextEntry::make('name'),
                TextEntry::make('description')->columnSpanFull()->placeholder('No description'),
                TextEntry::make('registration_fee')->money('IDR'),
                TextEntry::make('registration_open')->label('Registration')->formatStateUsing(
                    fn (bool $state): string => $state ? 'Open' : 'Closed',
                )->badge()->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                TextEntry::make('registration_start_at')->label('Registration starts')->dateTime()->placeholder('No start limit'),
                TextEntry::make('registration_end_at')->label('Registration closes')->dateTime()->placeholder('No end limit'),
                TextEntry::make('registrations_count')->state(fn (Competition $record): int => $record->registrations()->count())->label('Total registrations'),
            ])->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompetitions::route('/'),
            'create' => CreateCompetition::route('/create'),
            'view' => ViewCompetition::route('/{record}'),
            'edit' => EditCompetition::route('/{record}/edit'),
        ];
    }
}
