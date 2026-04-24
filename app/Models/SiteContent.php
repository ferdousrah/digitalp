<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteContent extends Model
{
    protected $fillable = ['page', 'key', 'label', 'value_en', 'value_bn', 'sort_order'];

    public static function get(string $page, string $key, string $fallback = ''): string
    {
        $locale    = app()->getLocale();
        $contents  = static::allCached();
        $item      = $contents->firstWhere(fn($c) => $c->page === $page && $c->key === $key);

        if (! $item) {
            return $fallback;
        }

        if ($locale === 'bn') {
            return $item->value_bn ?: $item->value_en ?: $fallback;
        }

        return $item->value_en ?: $fallback;
    }

    public static function allCached()
    {
        return cache()->remember('site_contents_all', 3600, fn () => static::orderBy('sort_order')->get());
    }

    public static function clearCache(): void
    {
        cache()->forget('site_contents_all');
    }
}
