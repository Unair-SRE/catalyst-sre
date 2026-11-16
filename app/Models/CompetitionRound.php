<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'competition_id',
    'name',
    'round_order',
    'starts_at',
    'ends_at',
    'published_at',
    'published_by',
])]
class CompetitionRound extends Model
{
    /** @use HasFactory<CompetitionRoundFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    /**
     * @return 'unscheduled'|'upcoming'|'open'|'closed'
     */
    public function windowState(): string
    {
        if ($this->starts_at === null || $this->ends_at === null) {
            return 'unscheduled';
        }

        return match (true) {
            now()->lt($this->starts_at) => 'upcoming',
            now()->gt($this->ends_at) => 'closed',
            default => 'open',
        };
    }

    public function competition(): BelongsTo
    {
        return $this->belongsTo(Competition::class);
    }

    public function results(): HasMany
    {
        return $this->hasMany(QualificationResult::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null;
    }
}
