<?php

namespace App\Filament\Resources\PaymentSettings;

use App\Filament\Resources\PaymentSettings\Pages\CreatePaymentSetting;
use App\Filament\Resources\PaymentSettings\Pages\EditPaymentSetting;
use App\Filament\Resources\PaymentSettings\Pages\ListPaymentSettings;
use App\Filament\Resources\PaymentSettings\Pages\ViewPaymentSetting;
use App\Filament\Resources\PaymentSettings\Schemas\PaymentSettingForm;
use App\Filament\Resources\PaymentSettings\Tables\PaymentSettingsTable;
use App\Models\PaymentSetting;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PaymentSettingResource extends Resource
{
    protected static ?string $model = PaymentSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Summit Sales Settings';

    protected static string|\UnitEnum|null $navigationGroup = 'Summit';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Summit sales setting';

    protected static ?string $pluralModelLabel = 'Summit Sales Settings';

    public static function form(Schema $schema): Schema
    {
        return PaymentSettingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentSettingsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Summit sales')->schema([
                TextEntry::make('is_active')->label('Sales status')
                    ->formatStateUsing(fn (bool $state): string => $state ? 'Open' : 'Closed')
                    ->badge()->color(fn (bool $state): string => $state ? 'success' : 'danger'),
                TextEntry::make('summit_ticket_price')->label('Ticket price')->money('IDR'),
                TextEntry::make('contact_person_name')->label('Contact person'),
                TextEntry::make('contact_person_whatsapp')->label('WhatsApp')->copyable(),
                TextEntry::make('created_at')->label('Created at')->dateTime(),
                TextEntry::make('updated_at')->label('Last updated')->dateTime(),
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
            'index' => ListPaymentSettings::route('/'),
            'create' => CreatePaymentSetting::route('/create'),
            'view' => ViewPaymentSetting::route('/{record}'),
            'edit' => EditPaymentSetting::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::salesAreOpen() ? 'OPEN' : 'CLOSED';
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::salesAreOpen() ? 'success' : 'danger';
    }

    private static function salesAreOpen(): bool
    {
        return PaymentSetting::query()
            ->where('is_active', true)
            ->whereNotNull('summit_ticket_price')
            ->where('summit_ticket_price', '>', 0)
            ->exists();
    }
}
