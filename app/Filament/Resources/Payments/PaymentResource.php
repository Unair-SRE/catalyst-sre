<?php

namespace App\Filament\Resources\Payments;

use App\Actions\Payments\RejectCompetitionPayment;
use App\Actions\Payments\VerifyCompetitionPayment;
use App\Enums\PaymentStatus;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Models\Payment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $navigationLabel = 'Competition Payments';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('registration.team.name')->label('Team')->searchable()->sortable(),
            TextColumn::make('registration.team.captain.name')->label('Captain')->searchable(),
            TextColumn::make('registration.competition.code')->label('Competition')->badge(),
            TextColumn::make('sender_name')->label('Sender')->placeholder('Not submitted'),
            TextColumn::make('status')->badge()->placeholder('NOT_SUBMITTED'),
            TextColumn::make('documents_submitted_at')->label('Documents submitted')->dateTime()->sortable(),
            TextColumn::make('review_note')->label('Review note')->limit(60)->wrap(),
            TextColumn::make('verified_at')->dateTime(),
            TextColumn::make('verifier.name')->label('Reviewed by'),
        ])->filters([
            SelectFilter::make('status')->options([
                PaymentStatus::WaitingVerification->value => 'Waiting verification',
                PaymentStatus::Verified->value => 'Verified',
                PaymentStatus::Rejected->value => 'Needs correction / rejected',
            ]),
        ])->recordActions([
            Action::make('openFolder')->label('Open team folder')
                ->icon(Heroicon::OutlinedFolderOpen)
                ->url(fn (Payment $record): string => route('dashboard.team.documents', $record->registration->team))
                ->openUrlInNewTab()
                ->visible(fn (Payment $record): bool => $record->registration->team->hasDocumentsFolder()),
            Action::make('viewDetails')->label('Review documents')
                ->modalHeading('Review team KTM and competition payment')
                ->modalContent(fn (Payment $record) => view('payment-details', [
                    'payment' => $record->loadMissing(['registration.team.captain', 'registration.team.members', 'registration.competition', 'verifier']),
                ]))
                ->modalSubmitAction(false)->modalCancelActionLabel('Close'),
            Action::make('approve')->label('Approve documents and payment')->color('success')
                ->requiresConfirmation()
                ->modalDescription('Confirm that the folder is accessible, every participant KTM is valid, and the payment proof matches this competition and fee.')
                ->visible(fn (Payment $record): bool => $record->status === PaymentStatus::WaitingVerification)
                ->action(fn (Payment $record) => app(VerifyCompetitionPayment::class)->handle(Auth::user(), $record)),
            Action::make('reject')->label('Request correction')->color('danger')
                ->form([Textarea::make('review_note')->label('What must the participant correct?')->required()->maxLength(2000)])
                ->visible(fn (Payment $record): bool => $record->status === PaymentStatus::WaitingVerification)
                ->action(fn (Payment $record, array $data) => app(RejectCompetitionPayment::class)->handle(Auth::user(), $record, $data['review_note'])),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListPayments::route('/')];
    }
}
