<?php

namespace App\Filament\Resources\PaymentSettings\Schemas;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PaymentSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Placeholder::make('qris_preview')
                    ->label('Static QRIS')
                    ->content(fn (): HtmlString => new HtmlString(
                        '<img class="max-h-80 rounded-lg border object-contain" src="'.e(asset(config('services.catalyst.qris_asset'))).'" alt="Catalyst payment QRIS">'
                        .'<p class="mt-2 text-sm text-gray-500">Managed as a static public asset. Replace public/'.e(config('services.catalyst.qris_asset')).' to update it.</p>',
                    ))
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
