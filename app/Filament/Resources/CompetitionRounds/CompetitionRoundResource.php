<?php

namespace App\Filament\Resources\CompetitionRounds;

use App\Actions\Results\PublishCompetitionRound;
use App\Enums\QualificationStatus;
use App\Enums\RegistrationStatus;
use App\Filament\Resources\CompetitionRounds\Pages\CreateCompetitionRound;
use App\Filament\Resources\CompetitionRounds\Pages\ListCompetitionRounds;
use App\Filament\Resources\CompetitionRounds\Pages\ViewCompetitionRound;
use App\Filament\Resources\CompetitionRounds\Schemas\CompetitionRoundForm;
use App\Filament\Resources\CompetitionRounds\Tables\CompetitionRoundsTable;
use App\Models\CompetitionRound;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class CompetitionRoundResource extends Resource
{
    protected static ?string $model = CompetitionRound::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static ?string $navigationLabel = 'Selection Rounds';

    protected static string|\UnitEnum|null $navigationGroup = 'Results';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return CompetitionRoundForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CompetitionRoundsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Round')->schema([
                TextEntry::make('competition.name')->label('Competition'),
                TextEntry::make('name')->label('Stage'),
                TextEntry::make('round_order')->label('Order'),
                TextEntry::make('published_at')->label('Published at')->dateTime()->placeholder('Not published'),
                TextEntry::make('publisher.name')->label('Published by')->placeholder('—'),
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
            'index' => ListCompetitionRounds::route('/'),
            'create' => CreateCompetitionRound::route('/create'),
            'view' => ViewCompetitionRound::route('/{record}'),
        ];
    }

    public static function publishAction(): Action
    {
        return Action::make('publish')
            ->label('Publish results')
            ->icon(Heroicon::OutlinedMegaphone)
            ->color('success')
            ->requiresConfirmation()
            ->modalDescription(fn (CompetitionRound $record): string => static::publishSummary($record))
            ->visible(fn (CompetitionRound $record): bool => ! $record->isPublished())
            ->action(function (CompetitionRound $record): void {
                app(PublishCompetitionRound::class)->handle(Auth::user(), $record);
                Notification::make()->title('Results published, captains notified')->success()->send();
            });
    }

    private static function publishSummary(CompetitionRound $record): string
    {
        $qualified = $record->results()->where('status', QualificationStatus::Qualified)->count();
        $notQualified = $record->results()->where('status', QualificationStatus::NotQualified)->count();
        $decided = $qualified + $notQualified;
        $pending = $record->competition->registrations()->where('status', RegistrationStatus::Verified)->count() - $decided;

        return "This will notify every captain by email and cannot be undone. Qualified: {$qualified}. Not qualified: {$notQualified}. Verified registrations without a result: {$pending}.";
    }
}
