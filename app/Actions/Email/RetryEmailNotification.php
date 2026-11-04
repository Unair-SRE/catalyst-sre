<?php

namespace App\Actions\Email;

use App\Enums\EmailStatus;
use App\Jobs\SendSummitOrderEmail;
use App\Models\EmailNotification;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class RetryEmailNotification
{
    public function handle(User $admin, EmailNotification $notification): EmailNotification
    {
        Gate::forUser($admin)->authorize('retry', $notification);

        return DB::transaction(function () use ($admin, $notification): EmailNotification {
            $locked = EmailNotification::query()
                ->lockForUpdate()
                ->findOrFail($notification->id);

            if ($locked->status !== EmailStatus::Failed) {
                throw ValidationException::withMessages([
                    'notification' => 'Only a failed notification can be retried.',
                ]);
            }

            if ($locked->summit_order_id === null) {
                throw ValidationException::withMessages([
                    'notification' => 'This notification has no linked order to retry.',
                ]);
            }

            $locked->forceFill([
                'status' => EmailStatus::Pending,
                'error_message' => null,
                'sent_at' => null,
            ])->save();

            Log::info('Email notification queued for retry.', [
                'operation' => 'email_notification.retry',
                'notification_id' => $locked->id,
                'admin_id' => $admin->id,
            ]);

            SendSummitOrderEmail::dispatch($locked->id, $locked->summit_order_id);

            return $locked->fresh();
        });
    }
}
