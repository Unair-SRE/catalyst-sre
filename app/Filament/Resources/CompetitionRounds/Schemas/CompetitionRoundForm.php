<?php

namespace App\Filament\Resources\CompetitionRounds\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class CompetitionRoundForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('competition_id')
                ->label('Competition')
                ->relationship('competition', 'name')
                ->required()
                ->searchable()
                ->preload(),
            TextInput::make('name')->required()->maxLength(120),
            TextInput::make('round_order')
                ->label('Order')
                ->required()
                ->numeric()
                ->minValue(1),
        ]);
    }
}
