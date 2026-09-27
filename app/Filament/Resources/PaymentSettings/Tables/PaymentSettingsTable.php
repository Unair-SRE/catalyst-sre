<?php

namespace App\Filament\Resources\PaymentSettings\Tables;

use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentSettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contact_person_name')->label('Contact person')->searchable(),
                TextColumn::make('contact_person_whatsapp')->label('WhatsApp'),
                TextColumn::make('summit_ticket_price')->label('Ticket price')->money('IDR'),
                IconColumn::make('is_active')->label('Sales open')->boolean(),
                TextColumn::make('updated_at')->label('Last updated')->dateTime()->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->emptyStateHeading('Summit sales are not configured')
            ->emptyStateDescription('Create a setting, enter the ticket price and contact person, then enable Summit ticket sales.');
    }
}
