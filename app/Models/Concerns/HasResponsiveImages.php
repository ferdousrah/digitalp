<?php

namespace App\Models\Concerns;

use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Shared helper for registering resized + WebP media conversions, so uploads are
 * stored in web-friendly sizes instead of serving the full-size original on the
 * storefront (a Core Web Vitals / bandwidth win).
 *
 * WebP variants are only registered when the image library can actually encode WebP
 * — guards against "Call to undefined function imagewebp()" on a GD build without
 * WebP support (the <picture> tags fall back to JPEG automatically).
 */
trait HasResponsiveImages
{
    /**
     * @param array<string,int> $sizes        e.g. ['medium' => 600, 'large' => 1200]
     * @param array<int,string> $collections   restrict to these collections (empty = all)
     */
    protected function registerResponsiveConversions(array $sizes, array $collections = []): void
    {
        $webpSupported = match (config('media-library.image_driver', 'gd')) {
            'imagick' => extension_loaded('imagick') && \Imagick::queryFormats('WEBP'),
            default   => function_exists('imagewebp'),
        };

        foreach ($sizes as $name => $width) {
            $conv = $this->addMediaConversion($name)
                ->width($width)
                ->quality(82)
                ->nonOptimized();
            if ($collections) {
                $conv->performOnCollections(...$collections);
            }

            if ($webpSupported) {
                $webp = $this->addMediaConversion($name . '_webp')
                    ->width($width)
                    ->quality(78)
                    ->format('webp');
                if ($collections) {
                    $webp->performOnCollections(...$collections);
                }
            }
        }
    }
}
