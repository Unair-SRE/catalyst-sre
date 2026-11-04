<?php

namespace App\Models;

use App\Enums\EmailStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'user_id',
    'summit_order_id',
    'recipient_email',
    'type',
    'status',
    'sent_at',
    'error_message',
])]
class EmailNotification extends Model
{
    protected function casts(): array
    {
        return [
            'status' => EmailStatus::class,
            'sent_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function summitOrder(): BelongsTo
    {
        return $this->belongsTo(SummitOrder::class);
    }

    public function markSent(): void
    {
        $this->forceFill([
            'status' => EmailStatus::Sent,
            'sent_at' => now(),
            'error_message' => null,
        ])->save();
    }

    public function markFailed(string $message): void
    {
        $this->forceFill([
            'status' => EmailStatus::Failed,
            'error_message' => substr($message, 0, 2000),
        ])->save();
    }
}
