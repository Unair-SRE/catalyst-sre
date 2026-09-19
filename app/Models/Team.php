<?php

namespace App\Models;

use Database\Factories\TeamFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['captain_id', 'name', 'institution', 'locked_at'])]
class Team extends Model
{
    /** @use HasFactory<TeamFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'locked_at' => 'datetime',
        ];
    }

    public function captain(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captain_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(TeamMember::class);
    }

    public function isLocked(): bool
    {
        return $this->locked_at !== null;
    }

    public function lock(): void
    {
        if (! $this->isLocked()) {
            $this->forceFill(['locked_at' => now()])->save();
        }
    }

    public function peopleCount(): int
    {
        return 1 + $this->members()->count();
    }

    public function hasCompleteKtm(): bool
    {
        return $this->captain->hasCompleteKtm()
            && ! $this->members()->where(function ($query): void {
                $query->whereNull('ktm_url')
                    ->orWhereNull('ktm_file_id')
                    ->orWhere('ktm_url', '')
                    ->orWhere('ktm_file_id', '');
            })->exists();
    }
}
