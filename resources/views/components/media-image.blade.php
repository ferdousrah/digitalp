@props([
    /** Any HasMedia model (Brand, Category, BlogPost, …) */
    'model',
    /** Media collection name, e.g. 'brand_logo' */
    'collection',
    /** Conversion base name registered on the model, e.g. 'medium' | 'large' | 'thumb' */
    'size' => 'medium',
    /** True for above-the-fold images — eager + high priority. False = lazy-load. */
    'eager' => false,
    'alt' => '',
    'class' => '',
    'style' => '',
    'width' => null,
    'height' => null,
])

@php
    // Prefer the resized conversion; fall back to the original upload if it isn't
    // generated yet (e.g. before `media-library:regenerate` runs on a fresh deploy).
    $media = $model->getFirstMedia($collection);
    $jpg   = $model->getFirstMediaUrl($collection, $size) ?: $model->getFirstMediaUrl($collection);
    $webp  = $model->getFirstMediaUrl($collection, $size . '_webp');
    // The image's own saved alt text wins over the caller's generic fallback.
    $alt   = ($media?->getCustomProperty('alt')) ?: $alt;
@endphp

@if($jpg)
    {{-- display:contents makes the <picture> layout-neutral, so the <img>'s own sizing
         (w-full/h-full, fixed px, object-fit) resolves exactly as it would without the wrapper. --}}
    <picture style="display:contents">
        @if($webp)<source type="image/webp" srcset="{{ $webp }}">@endif
        <img src="{{ $jpg }}"
             alt="{{ $alt }}"
             loading="{{ $eager ? 'eager' : 'lazy' }}"
             decoding="async"
             @if($eager) fetchpriority="high" @endif
             @if($width) width="{{ $width }}" @endif
             @if($height) height="{{ $height }}" @endif
             @if($class) class="{{ $class }}" @endif
             @if($style) style="{{ $style }}" @endif>
    </picture>
@else
    {{ $slot }}
@endif
