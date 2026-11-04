<?php

namespace App\Jobs;

use App\Mail\SummitOrderApprovedMail;
use App\Models\EmailNotification;
use App\Models\SummitOrder;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendSummitOrderEmail implements ShouldQueue
{
    use Dispatchable;

    public function __construct(
        public readonly int $notificationId,
        public readonly int $orderId,
    ) {}

    public function handle(): void
    {
        $notification = EmailNotification::query()->findOrFail($this->notificationId);
        $order = SummitOrder::query()->with(['tickets', 'user'])->findOrFail($this->orderId);

        try {
            Mail::to($notification->recipient_email)->send(new SummitOrderApprovedMail($order));
            $notification->markSent();
        } catch (Throwable $exception) {
            $notification->markFailed($exception->getMessage());
            report($exception);

            throw $exception;
        }
    }
}
