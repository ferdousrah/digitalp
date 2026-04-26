<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = [
        'source_path', 'target_path', 'status_code',
        'hits', 'last_hit_at', 'is_active', 'notes', 'is_auto',
    ];

    protected function casts(): array
    {
        return [
            'is_active'   => 'boolean',
            'is_auto'     => 'boolean',
            'last_hit_at' => 'datetime',
        ];
    }

    /** Normalise a path: leading slash, no trailing slash (except root), lowercase. */
    public static function normalize(string $path): string
    {
        // Strip query string + hash
        $path = strtok($path, '?');
        $path = strtok($path, '#');
        $path = '/' . ltrim((string) $path, '/');
        if ($path !== '/') {
            $path = rtrim($path, '/');
        }
        return $path;
    }

    public static function statusOptions(): array
    {
        return [
            301 => '301 — Moved Permanently (recommended)',
            302 => '302 — Found / Temporary',
            307 => '307 — Temporary Redirect (preserves method)',
            308 => '308 — Permanent Redirect (preserves method)',
        ];
    }
}
