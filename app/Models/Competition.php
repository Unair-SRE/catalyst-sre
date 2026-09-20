<?php

namespace App\Models;

use App\Enums\CompetitionCode;
use Database\Factories\CompetitionFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'code',
    'name',
    'description',
    'registration_fee',
    'registration_open',
    'registration_start_at',
    'registration_end_at',
])]
class Competition extends Model
{
    /** @use HasFactory<CompetitionFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'code' => CompetitionCode::class,
            'registration_fee' => 'decimal:2',
            'registration_open' => 'boolean',
            'registration_start_at' => 'datetime',
            'registration_end_at' => 'datetime',
        ];
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    public function acceptsRegistration(): bool
    {
        if (! $this->registration_open) {
            return false;
        }

        $now = now();

        return ($this->registration_start_at === null || $now->greaterThanOrEqualTo($this->registration_start_at))
            && ($this->registration_end_at === null || $now->lessThanOrEqualTo($this->registration_end_at));
    }
}
