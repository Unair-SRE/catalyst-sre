<?php

namespace App\Filament\Resources\CompetitionRounds\Tables;

use App\Filament\Resources\CompetitionRounds\CompetitionRoundResource;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CompetitionRoundsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('competition.code')->label('Competition')->badge()->sortable(),
                TextColumn::make('name')->label('Stage')->searchable()->sortable(),
                TextColumn::make('round_order')->label('Order')->sortable(),
                TextColumn::make('results_count')->label('Results')->counts('results')->sortable(),
                TextColumn::make('published_at')->label('Published')->dateTime()->sortable()->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('competition')
                    ->relationship('competition', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                CompetitionRoundResource::publishAction(),
            ])
            ->defaultSort('competition_id');
    }
}
