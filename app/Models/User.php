<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'avatar',
        'email',
        'phone',
        'password',
        'phone_verified_at',
    ];

    /** Public URL for the uploaded avatar, or null when none is set. */
    public function avatarUrl(): ?string
    {
        return $this->avatar
            ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->avatar)
            : null;
    }

    /** Saved delivery/billing addresses (default first, then newest). */
    public function addresses(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Address::class)->orderByDesc('is_default')->latest();
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    /**
     * Filament access is for staff only — anyone with a role assigned passes.
     * Phone-only customers (no roles) are blocked from /admin.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->roles()->exists();
    }

    public function isCustomer(): bool
    {
        return !$this->roles()->exists();
    }
}
