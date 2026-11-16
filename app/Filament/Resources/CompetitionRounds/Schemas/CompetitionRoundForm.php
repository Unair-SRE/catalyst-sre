<?php

namespace App\Filament\Resources\CompetitionRounds\Schemas;

use Filament\Forms\Components\DateTimePicker;
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
            DateTimePicker::make('starts_at')
                ->label('Submission opens')
                ->required()
                ->seconds(false)
                ->timezone('Asia/Jakarta'),
            DateTimePicker::make('ends_at')
                ->label('Submission closes')
                ->required()
                ->seconds(false)
                ->timezone('Asia/Jakarta')
                ->after('starts_at'),
        ]);
    }
}
