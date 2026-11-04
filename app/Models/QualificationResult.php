<?php

namespace App\Models;

use App\Enums\QualificationStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'registration_id',
    'competition_round_id',
    'status',
    'decided_by',
    'decided_at',
])]
class QualificationResult extends Model
{
    /** @use HasFactory<QualificationResultFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'status' => QualificationStatus::class,
            'decided_at' => 'datetime',
        ];
    }

    public function registration(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function round(): BelongsTo
    {
        return $this->belongsTo(CompetitionRound::class, 'competition_round_id');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }
}
