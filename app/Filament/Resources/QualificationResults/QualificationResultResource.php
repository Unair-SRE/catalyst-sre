<?php

namespace App\Filament\Resources\QualificationResults;

use App\Filament\Resources\QualificationResults\Pages\CreateQualificationResult;
use App\Filament\Resources\QualificationResults\Pages\ListQualificationResults;
use App\Filament\Resources\QualificationResults\Pages\ViewQualificationResult;
use App\Filament\Resources\QualificationResults\Schemas\QualificationResultForm;
use App\Filament\Resources\QualificationResults\Tables\QualificationResultsTable;
use App\Models\QualificationResult;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class QualificationResultResource extends Resource
{
    protected static ?string $model = QualificationResult::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCheckBadge;

    protected static ?string $navigationLabel = 'Qualification Results';

    protected static string|\UnitEnum|null $navigationGroup = 'Results';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return QualificationResultForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return QualificationResultsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Result')->schema([
                TextEntry::make('registration.team.name')->label('Team'),
                TextEntry::make('registration.competition.name')->label('Competition'),
                TextEntry::make('round.name')->label('Stage'),
                TextEntry::make('status')->badge(),
                TextEntry::make('decider.name')->label('Decided by')->placeholder('—'),
                TextEntry::make('decided_at')->label('Decided at')->dateTime()->placeholder('—'),
            ])->columns(2),
        ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQualificationResults::route('/'),
            'create' => CreateQualificationResult::route('/create'),
            'view' => ViewQualificationResult::route('/{record}'),
        ];
    }
}
