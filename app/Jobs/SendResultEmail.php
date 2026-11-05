<?php

namespace App\Jobs;

use App\Mail\ResultPublishedMail;
use App\Models\EmailNotification;
use App\Models\QualificationResult;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendResultEmail implements ShouldQueue
{
    use Dispatchable;

    public function __construct(
        public readonly int $notificationId,
        public readonly int $resultId,
    ) {}

    public function handle(): void
    {
        $notification = EmailNotification::query()->findOrFail($this->notificationId);
        $result = QualificationResult::query()
            ->with(['registration.team.captain', 'registration.competition', 'round'])
            ->findOrFail($this->resultId);

        try {
            Mail::to($notification->recipient_email)->send(new ResultPublishedMail($result));
            $notification->markSent();
        } catch (Throwable $exception) {
            $notification->markFailed($exception->getMessage());
            report($exception);

            throw $exception;
        }
    }
}
