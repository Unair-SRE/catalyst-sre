<?php

namespace App\Filament\Resources\SummitTickets;

use App\Actions\Summit\CheckInTicket;
use App\Enums\SummitTicketStatus;
use App\Filament\Resources\SummitTickets\Pages\ListSummitTickets;
use App\Models\SummitTicket;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class SummitTicketResource extends Resource
{
    protected static ?string $model = SummitTicket::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTicket;

    protected static ?string $navigationLabel = 'Summit Tickets';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_code')->label('Code')->searchable()->sortable(),
                TextColumn::make('holder_name')->label('Holder')->searchable()->sortable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('order.user.email')->label('Buyer')->searchable(),
                TextColumn::make('checked_in_at')->dateTime()->placeholder('—')->sortable(),
                TextColumn::make('checkedInBy.name')->label('Checked in by')->placeholder('—'),
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
                Action::make('checkIn')
                    ->label('Check in')
                    ->icon(Heroicon::OutlinedCheckBadge)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (SummitTicket $record): bool => $record->status === SummitTicketStatus::Active)
                    ->action(function (SummitTicket $record): void {
                        app(CheckInTicket::class)->handle(Auth::user(), $record);
                        Notification::make()->title('Ticket checked in')->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSummitTickets::route('/'),
        ];
    }
}
