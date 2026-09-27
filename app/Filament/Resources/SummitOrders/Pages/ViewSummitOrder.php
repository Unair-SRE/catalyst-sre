<?php

namespace App\Filament\Resources\SummitOrders\Pages;

use App\Actions\Summit\RejectSummitOrder;
use App\Actions\Summit\ReplaceSummitDrivePayment;
use App\Actions\Summit\VerifySummitOrder;
use App\Enums\SummitOrderStatus;
use App\Filament\Resources\SummitOrders\SummitOrderResource;
use App\Rules\GoogleDriveFolder;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

class ViewSummitOrder extends ViewRecord
{
    protected static string $resource = SummitOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('openFolder')->label('Open payment proof')
                ->icon(Heroicon::OutlinedFolderOpen)
                ->url(fn (): ?string => $this->record->payment_drive_url)
                ->openUrlInNewTab()
                ->visible(fn (): bool => $this->record->hasPaymentFolder()),
            Action::make('approve')->label('Approve payment')->color('success')
                ->requiresConfirmation()
                ->visible(fn (): bool => $this->record->payment_status === SummitOrderStatus::WaitingVerification)
                ->action(fn () => app(VerifySummitOrder::class)->handle(Auth::user(), $this->record)),
            Action::make('reject')->label('Reject payment')->color('danger')
                ->form([Textarea::make('review_note')->label('Reason and correction instructions')->required()->maxLength(2000)])
                ->visible(fn (): bool => $this->record->payment_status === SummitOrderStatus::WaitingVerification)
                ->action(fn (array $data) => app(RejectSummitOrder::class)->handle(Auth::user(), $this->record, $data['review_note'])),
            Action::make('replaceFolder')->label('Replace payment folder')->color('warning')
                ->form([
                    TextInput::make('payment_drive_url')->label('Corrected Google Drive folder')->url()->required()->maxLength(2048)->rules([new GoogleDriveFolder]),
                ])
                ->visible(fn (): bool => $this->record->payment_status === SummitOrderStatus::Rejected)
                ->action(fn (array $data) => app(ReplaceSummitDrivePayment::class)->handle(Auth::user(), $this->record, $data['payment_drive_url'])),
        ];
    }
}
