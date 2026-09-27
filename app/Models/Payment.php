<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'registration_id',
    'sender_name',
    'documents_submitted_at',
    'review_note',
    'payment_proof_url',
    'payment_proof_file_id',
    'status',
    'verified_by',
    'verified_at',
])]
class Payment extends Model
{
    /** @use HasFactory<PaymentFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'verified_at' => 'datetime',
            'documents_submitted_at' => 'datetime',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function hasProof(): bool
    {
        return $this->documents_submitted_at !== null
            && $this->registration->team->hasDocumentsFolder();
    }
}
