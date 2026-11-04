<?php

namespace App\Filament\Resources\EmailNotifications;

use App\Filament\Resources\EmailNotifications\Pages\ListEmailNotifications;
use App\Filament\Resources\EmailNotifications\Pages\ViewEmailNotification;
use App\Filament\Resources\EmailNotifications\Tables\EmailNotificationsTable;
use App\Models\EmailNotification;
use BackedEnum;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class EmailNotificationResource extends Resource
{
    protected static ?string $model = EmailNotification::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static ?string $navigationLabel = 'Email Log';

    protected static string|\UnitEnum|null $navigationGroup = 'Summit';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return EmailNotificationsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Delivery')->schema([
                TextEntry::make('recipient_email')->label('Recipient')->copyable(),
                TextEntry::make('type')->label('Type')->badge(),
                TextEntry::make('status')->badge(),
                TextEntry::make('sent_at')->label('Sent at')->dateTime()->placeholder('Not sent'),
                TextEntry::make('summit_order_id')->label('Summit order')->placeholder('—'),
                TextEntry::make('created_at')->label('Logged at')->dateTime(),
                TextEntry::make('error_message')->label('Error')->placeholder('—')->columnSpanFull(),
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
            'index' => ListEmailNotifications::route('/'),
            'view' => ViewEmailNotification::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
