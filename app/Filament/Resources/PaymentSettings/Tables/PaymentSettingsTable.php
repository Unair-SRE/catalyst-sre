<?php

namespace App\Filament\Resources\PaymentSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contact_person_name')->searchable(),
                TextColumn::make('contact_person_whatsapp'),
                TextColumn::make('summit_ticket_price')->money('IDR'),
                IconColumn::make('is_active')->label('Active')->boolean(),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
