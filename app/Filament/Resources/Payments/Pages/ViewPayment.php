<?php

namespace App\Filament\Resources\Payments\Pages;

use App\Actions\Payments\RejectCompetitionPayment;
use App\Actions\Payments\VerifyCompetitionPayment;
use App\Enums\PaymentStatus;
use App\Filament\Resources\Payments\PaymentResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('openFolder')->label('Open Google Drive')
                ->icon(Heroicon::OutlinedFolderOpen)
                ->url(fn (): ?string => $this->record->registration->team->documents_drive_url)
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->record->registration->team->hasDocumentsFolder()),
            Action::make('approve')->label('Approve payment')->color('success')
                ->requiresConfirmation()
                ->modalDescription('Confirm that the folder is accessible, every participant document is valid, and the payment proof matches this competition and fee.')
                ->visible(fn (): bool => $this->record->status === PaymentStatus::WaitingVerification)
                ->action(fn () => app(VerifyCompetitionPayment::class)->handle(Auth::user(), $this->record)),
            Action::make('reject')->label('Request correction')->color('danger')
                ->form([Textarea::make('review_note')->label('What must the participant correct?')->required()->maxLength(2000)])
                ->visible(fn (): bool => $this->record->status === PaymentStatus::WaitingVerification)
                ->action(fn (array $data) => app(RejectCompetitionPayment::class)->handle(Auth::user(), $this->record, $data['review_note'])),
        ];
    }
}
