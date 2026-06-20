@php
    /** @var \App\Models\Product $product */
    $images = $product->getMedia('product_images')->map(fn($m) => $m->getUrl('large'))->all();
    if (empty($images)) {
        $thumb = $product->getFirstMediaUrl('product_thumbnail', 'large');
        if ($thumb) $images = [$thumb];
    }

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Product',
        'name'        => $product->name,
        'sku'         => $product->sku,
        'description' => strip_tags((string) ($product->short_description ?? $product->description ?? '')),
        'image'       => array_values($images),
        'url'         => route('products.show', $product),
    ];

    if ($product->brand) {
        $schema['brand'] = [
            '@type' => 'Brand',
            'name'  => $product->brand->name,
        ];
    }

    $schema['offers'] = array_filter([
        '@type'         => 'Offer',
        'url'           => route('products.show', $product),
        'priceCurrency' => 'BDT',
        'price'         => (string) $product->price,
        'availability'  => $product->in_stock && $product->stock_quantity > 0
            ? 'https://schema.org/InStock'
            : 'https://schema.org/OutOfStock',
        'priceValidUntil' => now()->addYear()->toDateString(),
        'itemCondition'   => 'https://schema.org/NewCondition',
    ]);

    // Ratings + reviews (only approved) → eligible for star rich snippets
    $reviewStats = $product->reviewStats();
    if ($reviewStats['count'] > 0) {
        $schema['aggregateRating'] = [
            '@type'       => 'AggregateRating',
            'ratingValue' => (string) $reviewStats['avg'],
            'reviewCount' => (string) $reviewStats['count'],
            'bestRating'  => '5',
            'worstRating' => '1',
        ];

        $schema['review'] = $product->approvedReviews->take(10)->map(function ($r) {
            return array_filter([
                '@type'         => 'Review',
                'author'        => ['@type' => 'Person', 'name' => $r->name],
                'datePublished' => optional($r->created_at)->toDateString(),
                'name'          => $r->title,
                'reviewBody'    => $r->comment,
                'reviewRating'  => [
                    '@type'       => 'Rating',
                    'ratingValue' => (string) $r->rating,
                    'bestRating'  => '5',
                    'worstRating' => '1',
                ],
            ]);
        })->values()->all();
    }
@endphp

<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
