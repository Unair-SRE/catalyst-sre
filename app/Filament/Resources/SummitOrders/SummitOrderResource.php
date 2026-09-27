<?php

namespace App\Filament\Resources\SummitOrders;

use App\Enums\SummitOrderStatus;
use App\Filament\Resources\SummitOrders\Pages\ListSummitOrders;
use App\Filament\Resources\SummitOrders\Pages\ViewSummitOrder;
use App\Models\SummitOrder;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SummitOrderResource extends Resource
{
    protected static ?string $model = SummitOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?string $navigationLabel = 'Summit Orders';

    protected static string|\UnitEnum|null $navigationGroup = 'Summit';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Order')->formatStateUsing(fn ($state) => '#'.$state)->sortable(),
                TextColumn::make('user.name')->label('Buyer')->searchable()->sortable(),
                TextColumn::make('quantity')->label('Tickets')->sortable(),
                TextColumn::make('total_amount')->money('IDR')->sortable(),
                TextColumn::make('payment_status')->label('Status')->badge()->sortable(),
                TextColumn::make('payment_submitted_at')->label('Submitted')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('payment_status')->options([
                    SummitOrderStatus::WaitingPayment->value => 'Waiting payment',
                    SummitOrderStatus::WaitingVerification->value => 'Waiting verification',
                    SummitOrderStatus::Verified->value => 'Verified',
                    SummitOrderStatus::Rejected->value => 'Rejected',
                ]),
            ])
            ->recordActions([
                ViewAction::make()->label('Review'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Order')->schema([
                TextEntry::make('id')->label('Order number')->formatStateUsing(fn ($state): string => '#'.$state),
                TextEntry::make('payment_status')->label('Payment status')->badge(),
                TextEntry::make('quantity')->label('Tickets'),
                TextEntry::make('unit_price')->label('Price per ticket')->money('IDR'),
                TextEntry::make('total_amount')->label('Total')->money('IDR'),
                TextEntry::make('created_at')->label('Ordered at')->dateTime(),
            ])->columns(3),
            Section::make('Buyer and payment')->schema([
                TextEntry::make('user.name')->label('Buyer'),
                TextEntry::make('user.email')->label('Email')->copyable(),
                TextEntry::make('user.whatsapp')->label('WhatsApp')->copyable(),
                TextEntry::make('sender_name')->label('Account holder')->placeholder('Not submitted'),
                TextEntry::make('payment_submitted_at')->label('Submitted at')->dateTime()->placeholder('Not submitted'),
                TextEntry::make('verified_at')->label('Verified at')->dateTime()->placeholder('Not verified'),
                TextEntry::make('verifier.name')->label('Reviewed by')->placeholder('Not reviewed'),
                TextEntry::make('review_note')->label('Review note')->placeholder('No review note')->columnSpanFull(),
                TextEntry::make('payment_drive_url')->label('Google Drive payment folder')
                    ->url(fn (SummitOrder $record): ?string => $record->payment_drive_url)
                    ->openUrlInNewTab()->placeholder('Not submitted')->columnSpanFull(),
            ])->columns(2),
            Section::make('Tickets')->schema([
                RepeatableEntry::make('tickets')->label('')->schema([
                    TextEntry::make('ticket_code')->label('Ticket code')->copyable(),
                    TextEntry::make('holder_name')->label('Holder'),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('checked_in_at')->label('Checked in at')->dateTime()->placeholder('Not checked in'),
                ])->columns(4)->contained(false),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSummitOrders::route('/'),
            'view' => ViewSummitOrder::route('/{record}'),
        ];
    }
}
