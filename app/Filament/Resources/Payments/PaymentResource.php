<?php

namespace App\Filament\Resources\Payments;

use App\Enums\PaymentStatus;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Filament\Resources\Payments\Pages\ViewPayment;
use App\Models\Payment;
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

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'Competition';

    protected static ?int $navigationSort = 5;

    protected static ?string $navigationLabel = 'Competition Payments';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('registration.team.name')->label('Team')->searchable()->sortable(),
            TextColumn::make('registration.competition.code')->label('Competition')->badge(),
            TextColumn::make('sender_name')->label('Sender')->placeholder('Not submitted'),
            TextColumn::make('status')->badge()->placeholder('NOT_SUBMITTED'),
            TextColumn::make('documents_submitted_at')->label('Documents submitted')->dateTime()->sortable(),
        ])->filters([
            SelectFilter::make('status')->options([
                PaymentStatus::WaitingVerification->value => 'Waiting verification',
                PaymentStatus::Verified->value => 'Verified',
                PaymentStatus::Rejected->value => 'Needs correction / rejected',
            ]),
        ])->recordActions([
            ViewAction::make()->label('Review'),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Payment review')->schema([
                TextEntry::make('status')->badge()->placeholder('Not submitted'),
                TextEntry::make('sender_name')->label('Sender')->placeholder('Not submitted'),
                TextEntry::make('documents_submitted_at')->label('Submitted at')->dateTime()->placeholder('Not submitted'),
                TextEntry::make('verified_at')->label('Verified at')->dateTime()->placeholder('Not verified'),
                TextEntry::make('verifier.name')->label('Reviewed by')->placeholder('Not reviewed'),
                TextEntry::make('review_note')->label('Review note')->placeholder('No review note')->columnSpanFull(),
                TextEntry::make('registration.team.documents_drive_url')->label('Google Drive folder')
                    ->url(fn (Payment $record): ?string => $record->registration->team->documents_drive_url)
                    ->openUrlInNewTab()->placeholder('Not submitted')->columnSpanFull(),
            ])->columns(2),
            Section::make('Registration')->schema([
                TextEntry::make('registration.team.name')->label('Team'),
                TextEntry::make('registration.team.institution')->label('Institution'),
                TextEntry::make('registration.competition.name')->label('Competition'),
                TextEntry::make('registration.competition.registration_fee')->label('Amount due')->money('IDR'),
                TextEntry::make('registration.team.captain.name')->label('Captain'),
                TextEntry::make('registration.team.captain.email')->label('Captain email')->copyable(),
                RepeatableEntry::make('registration.team.members')->label('Members')->schema([
                    TextEntry::make('name'),
                    TextEntry::make('email')->copyable(),
                    TextEntry::make('whatsapp')->label('WhatsApp')->copyable(),
                ])->columns(3)->columnSpanFull(),
            ])->columns(2),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
            'view' => ViewPayment::route('/{record}'),
        ];
    }
}
