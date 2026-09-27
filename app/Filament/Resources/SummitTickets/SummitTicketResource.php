<?php

namespace App\Filament\Resources\SummitTickets;

use App\Enums\SummitTicketStatus;
use App\Filament\Resources\SummitTickets\Pages\ListSummitTickets;
use App\Filament\Resources\SummitTickets\Pages\ViewSummitTicket;
use App\Models\SummitTicket;
use BackedEnum;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SummitTicketResource extends Resource
{
    protected static ?string $model = SummitTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQrCode;

    protected static ?string $navigationLabel = 'Summit Check-in';

    protected static string|\UnitEnum|null $navigationGroup = 'Summit';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_code')->label('Ticket code')->searchable()->copyable(),
                TextColumn::make('holder_name')->label('Holder')->searchable()->sortable(),
                TextColumn::make('order.user.name')->label('Buyer')->searchable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('checked_in_at')->label('Checked in')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    SummitTicketStatus::WaitingPayment->value => 'Waiting payment',
                    SummitTicketStatus::WaitingVerification->value => 'Waiting verification',
                    SummitTicketStatus::Active->value => 'Active',
                    SummitTicketStatus::Used->value => 'Used',
                    SummitTicketStatus::Rejected->value => 'Rejected',
                ]),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ticket')->schema([
                TextEntry::make('ticket_code')->label('Ticket code')->copyable(),
                TextEntry::make('holder_name')->label('Holder'),
                TextEntry::make('status')->badge(),
                TextEntry::make('created_at')->label('Issued at')->dateTime(),
                TextEntry::make('checked_in_at')->label('Checked in at')->dateTime()->placeholder('Not checked in'),
                TextEntry::make('checkedInBy.name')->label('Checked in by')->placeholder('Not checked in'),
            ])->columns(2),
            Section::make('Order and buyer')->schema([
                TextEntry::make('order.id')->label('Order number')->formatStateUsing(fn ($state): string => '#'.$state),
                TextEntry::make('order.user.name')->label('Buyer'),
                TextEntry::make('order.user.email')->label('Buyer email')->copyable(),
                TextEntry::make('order.user.whatsapp')->label('Buyer WhatsApp')->copyable(),
                TextEntry::make('order.total_amount')->label('Order total')->money('IDR'),
                TextEntry::make('order.payment_status')->label('Payment status')->badge(),
            ])->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSummitTickets::route('/'),
            'view' => ViewSummitTicket::route('/{record}'),
        ];
    }
}
