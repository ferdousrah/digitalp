<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Slider extends Model implements HasMedia
{
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'subtitle',
        'button_text',
        'button_url',
        'link_url',
        'position',
        'is_active',
        'sort_order',
        'starts_at',
        'ends_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
        ];
    }

    /**
     * Scope a query to only include active sliders within date range.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->where(function (Builder $query) {
                $query->whereNull('starts_at')
                    ->orWhere('starts_at', '<=', now());
            })
            ->where(function (Builder $query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now());
            });
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('slide_image')
            ->singleFile();
    }

    /**
     * Resized + WebP variants so the hero (usually the page's LCP element) isn't the
     * full-size original upload. `medium` (800w) covers mobile + side banners; `large`
     * (1600w) covers the desktop slider with headroom for retina. WebP is registered
     * only when the image library can encode it — the <picture> tags fall back to JPEG.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $sizes = ['medium' => 800, 'large' => 1600];

        $webpSupported = match (config('media-library.image_driver', 'gd')) {
            'imagick' => extension_loaded('imagick') && \Imagick::queryFormats('WEBP'),
            default   => function_exists('imagewebp'),
        };

        foreach ($sizes as $name => $width) {
            $this->addMediaConversion($name)
                ->width($width)
                ->quality(82)
                ->nonOptimized()
                ->performOnCollections('slide_image');

            if ($webpSupported) {
                $this->addMediaConversion($name . '_webp')
                    ->width($width)
                    ->quality(78)
                    ->format('webp')
                    ->performOnCollections('slide_image');
            }
        }
    }
}
