<?php

namespace App\Filament\Resources\PaymentSettings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class PaymentSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('qris_url')
                    ->label('QRIS URL')
                    ->url()
                    ->columnSpanFull(),
                TextInput::make('contact_person_name')->maxLength(120),
                TextInput::make('contact_person_whatsapp')->maxLength(20),
                TextInput::make('summit_ticket_price')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('IDR'),
                Toggle::make('is_active')->label('Active'),
            ]);
    }
}
