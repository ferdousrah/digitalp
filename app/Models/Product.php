<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia, HasSlug, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'sku',
        'short_description',
        'description',
        'price',
        'compare_price',
        'cost_price',
        'brand_id',
        'product_type',
        'is_active',
        'is_featured',
        'is_new_arrival',
        'is_best_seller',
        'in_stock',
        'stock_quantity',
        'min_stock_quantity',
        'meta_title',
        'meta_description',
        'specifications',
        'custom_tabs',
        'faqs',
        'videos',
        'thumbnail_video',
        'weight',
        'dimensions',
        'warranty_info',
        'sort_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'compare_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
            'is_active'      => 'boolean',
            'is_featured'    => 'boolean',
            'is_new_arrival' => 'boolean',
            'is_best_seller' => 'boolean',
            'in_stock'       => 'boolean',
            'specifications'  => 'array',
            'custom_tabs'     => 'array',
            'faqs'            => 'array',
            'videos'          => 'array',
            'thumbnail_video' => 'array',
        ];
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        // Keys must be real columns so Scout's DATABASE engine (used locally) works too.
        return [
            'name'              => $this->name,
            'short_description' => $this->short_description,
            'sku'               => $this->sku,
        ];
    }

    /** Only active products belong in the search index. */
    public function shouldBeSearchable(): bool
    {
        return (bool) $this->is_active;
    }

    /** All reviews (any status). */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /** Public-facing approved reviews, newest first. */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', 'approved')->latest();
    }

    /** Customer questions (all). */
    public function questions(): HasMany
    {
        return $this->hasMany(ProductQuestion::class);
    }

    /** Answered + published questions shown on the product page, newest first. */
    public function publishedQuestions(): HasMany
    {
        return $this->hasMany(ProductQuestion::class)->published()->latest('answered_at');
    }

    /** True if the user has a non-cancelled order for this product (paid, or delivered/completed). */
    public function purchasedBy(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return OrderItem::where('product_id', $this->id)
            ->whereHas('order', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->where(function ($q2) {
                      $q2->whereIn('status', ['delivered', 'completed'])
                         ->orWhere('payment_status', 'paid');
                  });
            })->exists();
    }

    /** True if the user already reviewed this product (one review per buyer). */
    public function reviewedBy(?User $user): bool
    {
        return $user ? $this->reviews()->where('user_id', $user->id)->exists() : false;
    }

    /** ['count' => int, 'avg' => float] for approved reviews (uses the loaded relation if present). */
    public function reviewStats(): array
    {
        $reviews = $this->relationLoaded('approvedReviews')
            ? $this->approvedReviews
            : $this->approvedReviews()->get();

        return [
            'count' => $reviews->count(),
            'avg'   => $reviews->count() ? round($reviews->avg('rating'), 1) : 0.0,
        ];
    }

    /**
     * The categories that belong to the product.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Get the brand that owns the product.
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Get the attribute values for the product.
     */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(ProductAttributeValue::class);
    }

    /**
     * Get the wishlists for the product.
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include in-stock products.
     */
    public function scopeInStock(Builder $query): Builder
    {
        return $query->where('in_stock', true);
    }

    /**
     * Register media collections.
     */
    public function registerMediaCollections(): void
    {
        // No useFallbackUrl: the missing /images/placeholder-product.jpg 404'd. When a
        // product has no image, getFirstMediaUrl() now returns '' and the <x-product-image>
        // / <x-media-image> component renders its own inline-SVG placeholder instead.
        $this->addMediaCollection('product_images');

        // Multiple thumbnails — the product card shows them as a hover slider when >1.
        $this->addMediaCollection('product_thumbnail');
    }

    /**
     * Register media conversions.
     *
     * Each conversion is registered twice — once in the original format (jpg/png
     * for browser-fallback) and once as WebP (~25–35% smaller). The Blade
     * component <x-product-image> emits a <picture> tag that prefers WebP and
     * falls back to the original.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $sizes = ['thumb' => 300, 'medium' => 600, 'large' => 1200];

        // Register WebP variants only when the image library can actually encode WebP.
        // Prevents "Call to undefined function imagewebp()" on servers whose GD was built
        // without WebP support; the front-end <picture> tags fall back to JPEG automatically.
        $webpSupported = match (config('media-library.image_driver', 'gd')) {
            'imagick' => extension_loaded('imagick') && \Imagick::queryFormats('WEBP'),
            default   => function_exists('imagewebp'),
        };

        foreach ($sizes as $name => $size) {
            $this->addMediaConversion($name)
                ->width($size)
                ->height($size)
                ->sharpen(10)
                ->quality(82)
                ->nonOptimized();

            if ($webpSupported) {
                $this->addMediaConversion($name . '_webp')
                    ->width($size)
                    ->height($size)
                    ->sharpen(10)
                    ->quality(78)
                    ->format('webp');
            }
        }
    }
}
