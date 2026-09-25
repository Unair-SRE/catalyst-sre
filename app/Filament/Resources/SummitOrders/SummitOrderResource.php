<?php

namespace App\Filament\Resources\SummitOrders;

use App\Actions\Summit\RejectSummitOrder;
use App\Actions\Summit\VerifySummitOrder;
use App\Contracts\SummitPaymentStorage;
use App\Enums\SummitOrderStatus;
use App\Filament\Resources\SummitOrders\Pages\ListSummitOrders;
use App\Models\SummitOrder;
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

class SummitOrderResource extends Resource
{
    protected static ?string $model = SummitOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingBag;

    protected static ?string $navigationLabel = 'Summit Orders';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->label('Order')->sortable(),
                TextColumn::make('user.name')->label('Buyer')->searchable()->sortable(),
                TextColumn::make('user.email')->label('Email')->searchable(),
                TextColumn::make('quantity')->sortable(),
                TextColumn::make('total_amount')->money('IDR')->sortable(),
                TextColumn::make('sender_name')->label('Sender')->placeholder('Not submitted')->searchable(),
                TextColumn::make('payment_status')
                    ->badge()
                    ->sortable(),
                TextColumn::make('verified_at')->dateTime()->placeholder('—')->sortable(),
                TextColumn::make('verifier.name')->label('Verified by')->placeholder('—'),
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
                Action::make('viewProof')
                    ->label('View proof')
                    ->icon(Heroicon::OutlinedEye)
                    ->modalHeading('Summit payment proof')
                    ->modalContent(fn (SummitOrder $record) => view('filament.summit-orders.proof', [
                        'order' => $record,
                        'proofUrl' => static::proofPreviewUrl($record),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->visible(fn (SummitOrder $record): bool => $record->hasProof()),
                Action::make('approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (SummitOrder $record): bool => $record->payment_status === SummitOrderStatus::WaitingVerification)
                    ->action(function (SummitOrder $record): void {
                        app(VerifySummitOrder::class)->handle(Auth::user(), $record);
                        Notification::make()->title('Order verified, tickets activated')->success()->send();
                    }),
                Action::make('reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (SummitOrder $record): bool => $record->payment_status === SummitOrderStatus::WaitingVerification)
                    ->action(function (SummitOrder $record): void {
                        app(RejectSummitOrder::class)->handle(Auth::user(), $record);
                        Notification::make()->title('Order rejected')->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSummitOrders::route('/'),
        ];
    }

    private static function proofPreviewUrl(SummitOrder $order): ?string
    {
        if (! $order->hasProof()) {
            return null;
        }

        return app(SummitPaymentStorage::class)->temporaryUrl($order->payment_proof_url);
    }
}
