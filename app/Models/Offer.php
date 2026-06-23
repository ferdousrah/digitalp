<?php

namespace App\Models;

use App\Models\Concerns\HasResponsiveImages;
use App\Models\Concerns\ReusableSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Offer extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia, HasSlug, ReusableSlug, HasResponsiveImages;

    protected $fillable = [
        'title', 'slug', 'subtitle', 'body',
        'starts_at', 'ends_at', 'cta_label', 'cta_url',
        'is_active', 'sort_order', 'meta_title', 'meta_description',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at'   => 'datetime',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()->generateSlugsFrom('title')->saveSlugsTo('slug');
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('offer_banner')->singleFile();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->registerResponsiveConversions(['medium' => 700, 'large' => 1400]);
    }

    /** Active offers whose window (if set) is currently open, ordered for display. */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->orderBy('sort_order')
            ->orderByDesc('starts_at');
    }

    /** True when ends_at is set and still in the future (used to show the countdown). */
    public function getIsLiveAttribute(): bool
    {
        return $this->ends_at && $this->ends_at->isFuture();
    }
}
