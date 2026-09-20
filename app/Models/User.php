<?php

namespace App\Models;

use App\Actions\Auth\IssueEmailVerificationOtp;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'whatsapp', 'password', 'ktm_url', 'ktm_file_id'])]
#[Hidden(['password', 'remember_token', 'otp_hash'])]
class User extends Authenticatable implements FilamentUser, MustVerifyEmailContract
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'otp_expires_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }

    public function isParticipant(): bool
    {
        return $this->role === UserRole::Participant;
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin' && $this->isAdmin();
    }

    public function captainedTeam(): HasOne
    {
        return $this->hasOne(Team::class, 'captain_id');
    }

    public function hasCompleteKtm(): bool
    {
        return filled($this->ktm_url) && filled($this->ktm_file_id);
    }

    public function sendEmailVerificationNotification(): void
    {
        app(IssueEmailVerificationOtp::class)->handle($this);
    }
}
