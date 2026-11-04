<?php

namespace App\Filament\Resources\EmailNotifications\Tables;

use App\Actions\Email\RetryEmailNotification;
use App\Enums\EmailStatus;
use App\Models\EmailNotification;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class EmailNotificationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('recipient_email')->label('Recipient')->searchable(),
                TextColumn::make('type')->label('Type')->badge(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('sent_at')->label('Sent')->dateTime()->sortable()->placeholder('—'),
                TextColumn::make('created_at')->label('Logged')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    EmailStatus::Pending->value => 'Pending',
                    EmailStatus::Sent->value => 'Sent',
                    EmailStatus::Failed->value => 'Failed',
                ]),
                SelectFilter::make('type')->options([
                    'SUMMIT_ORDER_APPROVED' => 'Summit order approved',
                ]),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('retry')
                    ->label('Retry')
                    ->icon(Heroicon::OutlinedArrowPath)
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn (EmailNotification $record): bool => $record->status === EmailStatus::Failed
                        && $record->summit_order_id !== null)
                    ->action(function (EmailNotification $record): void {
                        app(RetryEmailNotification::class)->handle(Auth::user(), $record);
                        Notification::make()->title('Email queued for retry')->success()->send();
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
