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
        return [
            'name' => $this->name,
            'short_description' => $this->short_description,
            'sku' => $this->sku,
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
        $this->addMediaCollection('product_images')
            ->useFallbackUrl('/images/placeholder-product.jpg');

        $this->addMediaCollection('product_thumbnail')
            ->singleFile();
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

        foreach ($sizes as $name => $size) {
            $this->addMediaConversion($name)
                ->width($size)
                ->height($size)
                ->sharpen(10)
                ->quality(82)
                ->nonOptimized();

            $this->addMediaConversion($name . '_webp')
                ->width($size)
                ->height($size)
                ->sharpen(10)
                ->quality(78)
                ->format('webp');
        }
    }
}
