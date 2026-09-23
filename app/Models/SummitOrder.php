<?php

namespace App\Models;

use App\Enums\SummitOrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'quantity',
    'unit_price',
    'total_amount',
    'sender_name',
    'payment_proof_url',
    'payment_proof_file_id',
    'payment_status',
    'verified_by',
    'verified_at',
])]
class SummitOrder extends Model
{
    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'payment_status' => SummitOrderStatus::class,
            'verified_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(SummitTicket::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function hasProof(): bool
    {
        return filled($this->payment_proof_url) && filled($this->payment_proof_file_id);
    }
}
