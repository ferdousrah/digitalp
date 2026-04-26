<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PhoneOtpCode extends Model
{
    protected $fillable = [
        'phone',
        'code_hash',
        'attempts',
        'expires_at',
        'consumed_at',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'expires_at'  => 'datetime',
            'consumed_at' => 'datetime',
        ];
    }

    public function isExpired(): bool
    {
        return $this->expires_at?->isPast() ?? true;
    }

    public function isConsumed(): bool
    {
        return $this->consumed_at !== null;
    }
}
