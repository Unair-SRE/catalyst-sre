<?php

namespace App\Filament\Resources\Payments;

use App\Actions\Payments\RejectCompetitionPayment;
use App\Actions\Payments\ReplaceCompetitionPaymentProof;
use App\Actions\Payments\VerifyCompetitionPayment;
use App\Enums\PaymentStatus;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Models\Payment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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
        return $table
            ->columns([
                ImageColumn::make('proof_preview')
                    ->label('Proof')
                    ->state(fn (Payment $record): ?string => static::proofPreviewUrl($record))
                    ->square()
                    ->imageSize(48)
                    ->checkFileExistence(false),
                TextColumn::make('registration.team.name')->label('Team')->searchable()->sortable(),
                TextColumn::make('registration.team.captain.name')->label('Captain')->searchable(),
                TextColumn::make('registration.competition.code')->label('Competition')->badge()->sortable(),
                TextColumn::make('sender_name')->label('Sender')->placeholder('Not submitted')->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?PaymentStatus $state): string => $state?->value ?? 'NOT_SUBMITTED'),
                TextColumn::make('verified_at')->dateTime()->placeholder('—')->sortable(),
                TextColumn::make('verifier.name')->label('Verified by')->placeholder('—'),
                TextColumn::make('updated_at')->label('Updated')->dateTime()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')->options([
                    PaymentStatus::WaitingVerification->value => 'Waiting verification',
                    PaymentStatus::Verified->value => 'Verified',
                    PaymentStatus::Rejected->value => 'Rejected',
                ]),
                SelectFilter::make('competition')
                    ->relationship('registration.competition', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                Action::make('viewDetails')
                    ->label('View details')
                    ->icon(Heroicon::OutlinedEye)
                    ->modalHeading('Competition payment details')
                    ->modalContent(fn (Payment $record) => view('payment-details', [
                        'payment' => $record->loadMissing(['registration.team.captain', 'registration.competition', 'verifier']),
                        'proofUrl' => static::proofPreviewUrl($record),
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->visible(fn (Payment $record): bool => $record->hasProof()),
                Action::make('approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Payment $record): bool => in_array($record->status, [PaymentStatus::WaitingVerification, PaymentStatus::Rejected], true))
                    ->action(function (Payment $record): void {
                        app(VerifyCompetitionPayment::class)->handle(Auth::user(), $record);
                        Notification::make()->title('Payment verified')->success()->send();
                    }),
                Action::make('reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Payment $record): bool => $record->status === PaymentStatus::WaitingVerification)
                    ->action(function (Payment $record): void {
                        app(RejectCompetitionPayment::class)->handle(Auth::user(), $record);
                        Notification::make()->title('Payment rejected')->success()->send();
                    }),
                Action::make('replaceProof')
                    ->label('Replace proof')
                    ->icon(Heroicon::OutlinedArrowUpTray)
                    ->visible(fn (Payment $record): bool => $record->status === PaymentStatus::Rejected)
                    ->requiresConfirmation()
                    ->form([
                        TextInput::make('sender_name')->required()->maxLength(120),
                        FileUpload::make('proof')
                            ->required()
                            ->acceptedFileTypes(['image/jpeg', 'image/png'])
                            ->maxSize(10240)
                            ->storeFiles(false),
                    ])
                    ->fillForm(fn (Payment $record): array => ['sender_name' => $record->sender_name])
                    ->action(function (Payment $record, array $data): void {
                        $proof = $data['proof'];

                        if (! $proof instanceof TemporaryUploadedFile) {
                            return;
                        }

                        app(ReplaceCompetitionPaymentProof::class)->handle(
                            Auth::user(),
                            $record,
                            $data['sender_name'],
                            $proof,
                        );
                        Notification::make()->title('Payment proof replaced')->success()->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
        ];
    }

    private static function proofPreviewUrl(Payment $payment): ?string
    {
        if (! $payment->hasProof()) {
            return null;
        }

        if (str_starts_with($payment->payment_proof_file_id, 'seed-')) {
            return asset('images/brand/catalyst-mark.png');
        }

        return route('dashboard.competition-payment.proof', $payment);
    }
}
