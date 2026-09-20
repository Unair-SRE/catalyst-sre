<?php

namespace App\Filament\Resources\Competitions;

use App\Enums\CompetitionCode;
use App\Filament\Resources\Competitions\Pages\CreateCompetition;
use App\Filament\Resources\Competitions\Pages\EditCompetition;
use App\Filament\Resources\Competitions\Pages\ListCompetitions;
use App\Models\Competition;
use BackedEnum;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class CompetitionResource extends Resource
{
    protected static ?string $model = Competition::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

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
                TextColumn::make('registration_start_at')->dateTime()->sortable(),
                TextColumn::make('registration_end_at')->dateTime()->sortable(),
                TextColumn::make('registrations_count')->counts('registrations')->label('Registrations'),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCompetitions::route('/'),
            'create' => CreateCompetition::route('/create'),
            'edit' => EditCompetition::route('/{record}/edit'),
        ];
    }
}
