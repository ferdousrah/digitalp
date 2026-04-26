@props([
    /** \App\Models\Product $product */
    'product',
    /** thumb | medium | large */
    'size' => 'medium',
    /** product_thumbnail | product_images */
    'collection' => 'product_thumbnail',
    /** True for above-the-fold images (hero / first card). False = lazy-load. */
    'eager' => false,
    /** Custom alt text. Falls back to product name. */
    'alt' => null,
    'class' => '',
    'style' => '',
])

@php
    $jpg  = $product->getFirstMediaUrl($collection, $size);
    $webp = $product->getFirstMediaUrl($collection, $size . '_webp');
    if (!$jpg && $collection === 'product_thumbnail') {
        // Fall back to first image from gallery if thumbnail is missing
        $jpg  = $product->getFirstMediaUrl('product_images', $size);
        $webp = $product->getFirstMediaUrl('product_images', $size . '_webp');
    }

    $altText = $alt ?? $product->name;
    $loading = $eager ? 'eager' : 'lazy';
    $fetchPriority = $eager ? 'high' : 'auto';

    // Width/height help the browser reserve space and avoid CLS (Cumulative Layout Shift)
    $dim = ['thumb' => 300, 'medium' => 600, 'large' => 1200][$size] ?? 600;
@endphp

@if($jpg)
    <picture>
        @if($webp)
            <source srcset="{{ $webp }}" type="image/webp">
        @endif
        <img src="{{ $jpg }}"
             alt="{{ $altText }}"
             width="{{ $dim }}"
             height="{{ $dim }}"
             loading="{{ $loading }}"
             decoding="async"
             fetchpriority="{{ $fetchPriority }}"
             class="{{ $class }}"
             style="{{ $style }}">
    </picture>
@else
    {{-- Placeholder so we still keep layout stable --}}
    <div class="{{ $class }}" style="aspect-ratio:1/1; background:linear-gradient(135deg,#f9fafb,#f3f4f6); display:flex; align-items:center; justify-content:center; {{ $style }}">
        <svg style="width:48px; height:48px; color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
    </div>
@endif
