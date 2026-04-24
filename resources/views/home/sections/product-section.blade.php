@props(['section', 'products'])
@php
    $filter     = $section->extra['product_filter'] ?? 'featured';
    $categoryId = $section->extra['category_id'] ?? null;

    $viewAllUrl = match($filter) {
        'new_arrival'  => route('products.index', ['filter' => 'new_arrival']),
        'best_seller'  => route('products.index', ['filter' => 'best_seller']),
        'category'     => $categoryId ? route('categories.show', $categoryId) : route('products.index'),
        default        => route('products.index'),
    };
@endphp

<section style="background:{{ $section->bg_color }}; padding:{{ $section->padding_y }}px 0;">
    <div class="container-custom px-4 sm:px-6 lg:px-8">

        @include('home.sections._section-header', [
            'section'    => $section,
            'viewAllUrl' => $viewAllUrl,
        ])

        @if($section->display_type === 'carousel')
        <div class="hs-carousel-wrap" style="position:relative;">
            <div class="hs-carousel" id="prod-carousel-{{ $section->id }}"
                style="display:flex; align-items:start; gap:16px; overflow-x:auto; scroll-snap-type:x mandatory; scrollbar-width:none; -ms-overflow-style:none; padding-bottom:4px;"
                onmousedown="hsCarouselDragStart(event,this)" onmousemove="hsCarouselDragMove(event,this)" onmouseup="hsCarouselDragEnd(event,this)" onmouseleave="hsCarouselDragEnd(event,this)">
                @foreach($products as $product)
                <div style="flex:0 0 calc((100% - {{ ($section->desktop_visible - 1) * 16 }}px) / {{ $section->desktop_visible }}); scroll-snap-align:start; min-width:0;" class="prod-item-{{ $section->id }}">
                    <x-product-card :product="$product" />
                </div>
                @endforeach
            </div>
            @include('home.sections._carousel-nav', ['id' => 'prod-carousel-'.$section->id])
        </div>
        <style>
        #prod-carousel-{{ $section->id }}::-webkit-scrollbar { display:none; }
        @media(max-width:767px) {
            #prod-carousel-{{ $section->id }} .prod-item-{{ $section->id }} {
                flex: 0 0 calc((100% - {{ ($section->mobile_visible - 1) * 16 }}px) / {{ $section->mobile_visible }}) !important;
            }
        }
        </style>

        @else
        @php $maxItems = $section->desktop_columns * $section->rows; @endphp
        <div class="prod-grid-{{ $section->id }}" style="display:grid; grid-template-columns:repeat({{ $section->desktop_columns }}, 1fr); gap:20px; align-items:start; justify-items:{{ $section->extra['content_align'] ?? 'stretch' }};">
            @foreach($products->take($maxItems) as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
        <style>
        @media(max-width:767px) {
            .prod-grid-{{ $section->id }} { grid-template-columns: repeat({{ $section->mobile_columns }}, 1fr) !important; }
        }
        </style>
        @endif

    </div>
</section>
