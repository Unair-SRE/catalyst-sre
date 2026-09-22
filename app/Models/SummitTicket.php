<?php

namespace App\Models;

use App\Enums\SummitTicketStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'summit_order_id',
    'holder_name',
    'ticket_code',
    'pdf_url',
    'pdf_file_id',
    'status',
    'checked_in_by',
    'checked_in_at',
])]
class SummitTicket extends Model
{
    protected function casts(): array
    {
        return [
            'status' => SummitTicketStatus::class,
            'checked_in_at' => 'datetime',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(SummitOrder::class, 'summit_order_id');
    }

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }
}
