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
                        .'<p class="mt-2 text-sm text-gray-500">QRIS yang saat ini ditampilkan kepada pembeli. Hubungi pengelola sistem jika gambar perlu diperbarui.</p>',
                    ))
                    ->columnSpanFull(),
                TextInput::make('contact_person_name')
                    ->label('Summit contact person')
                    ->required()
                    ->maxLength(120)
                    ->helperText('Shown to buyers when a Summit payment is rejected.'),
                TextInput::make('contact_person_whatsapp')
                    ->label('Contact WhatsApp')
                    ->tel()
                    ->required()
                    ->maxLength(20)
                    ->helperText('Use a number that can be opened through wa.me, for example 6281234567890.'),
                TextInput::make('summit_ticket_price')
                    ->label('Summit ticket price')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->prefix('IDR')
                    ->helperText('Price for one ticket. The order total is calculated from this value.'),
                Toggle::make('is_active')
                    ->label('Open Summit ticket sales')
                    ->default(true)
                    ->helperText('Only one configuration can be active. Turning this off closes new Summit purchases.'),
            ]);
    }
}
