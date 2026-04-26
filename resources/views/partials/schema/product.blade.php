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
@endphp

<script type="application/ld+json">
{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}
</script>
