@extends('layouts.app')
@section('title', $product->meta_title ?? $product->name . '')
@section('meta_description', $product->meta_description ?? \Illuminate\Support\Str::limit(strip_tags((string) ($product->short_description ?? $product->description)), 160))

@php
    app(\App\Services\SeoService::class)
        ->ogType('product')
        ->image($product->getFirstMediaUrl('product_thumbnail', 'large') ?: $product->getFirstMediaUrl('product_images', 'large'))
        ->canonical(route('products.show', $product));
@endphp

@push('seo')
    @include('partials.schema.product', ['product' => $product])
    @include('partials.schema.breadcrumbs', ['items' => [
        ['label' => 'Home',     'url' => url('/')],
        ['label' => 'Products', 'url' => route('products.index')],
        ['label' => $product->name],
    ]])
    @if(!empty($product->faqs))
        @include('partials.schema.faq', ['faqs' => collect($product->faqs)->map(fn ($f) => (object) $f)])
    @endif
    @if(!empty($product->videos))
        @include('partials.schema.product-videos', ['product' => $product])
    @endif
@endpush

@section('content')
@php
    $tc = \App\Filament\Pages\TemplateSettings::defaults();
    foreach ($tc as $k => $d) { $tc[$k] = \App\Services\SettingService::get($k, $d); }
    $reviewStats = $product->reviewStats();
    $starPath = 'M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z';
@endphp
@include('components.breadcrumb', ['items' => [['label' => 'Products', 'url' => route('products.index')], ['label' => $product->name]]])

<div class="container-custom px-4 sm:px-6 lg:px-8 pb-16">
    <div class="grid lg:grid-cols-2 gap-8 lg:gap-12 relative">
        <!-- Product Images: Vertical Thumbs + Main Image -->
        <div>
            @php
                $allMedia = $product->getMedia('product_images');
                $mainImage = $product->getFirstMediaUrl('product_images', 'large') ?: $product->getFirstMediaUrl('product_thumbnail', 'large');
                $fullImage = $product->getFirstMediaUrl('product_images') ?: $product->getFirstMediaUrl('product_thumbnail');

                // Normalise videos with parsed YouTube IDs / file URLs
                $videos = collect($product->videos ?? [])->map(function ($v) {
                    $type = $v['type'] ?? null;
                    $out = ['type' => $type, 'title' => $v['title'] ?? null, 'youtube_id' => null, 'src' => null, 'thumb' => null];
                    if (in_array($type, ['youtube', 'youtube_reel']) && !empty($v['url'])) {
                        if (preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/|embed/|v/)|youtu\.be/)([\w-]{11})~', $v['url'], $m)) {
                            $out['youtube_id'] = $m[1];
                            // Stealth embed params: no branding, no controls, autoplay muted loop — looks self-hosted
                            $out['src'] = 'https://www.youtube-nocookie.com/embed/' . $m[1]
                                . '?autoplay=1&mute=1&loop=1&playlist=' . $m[1]
                                . '&controls=0&showinfo=0&modestbranding=1&rel=0&playsinline=1'
                                . '&iv_load_policy=3&disablekb=1&fs=0&cc_load_policy=0';
                            $out['thumb'] = 'https://img.youtube.com/vi/' . $m[1] . '/mqdefault.jpg';
                        }
                    } elseif ($type === 'upload' && !empty($v['file'])) {
                        $out['src'] = \Illuminate\Support\Facades\Storage::disk('public')->url($v['file']);
                    }
                    return $out;
                })->filter(fn ($v) => !empty($v['src']))->values();
                $totalThumbs = $allMedia->count() + $videos->count();
            @endphp
            <div class="pd-gallery" style="display: flex; gap: 12px;">
                {{-- Vertical Thumbnails --}}
                @if($totalThumbs > 1)
                <div id="thumb-strip" style="display: flex; flex-direction: column; gap: 8px; width: 70px; min-width: 70px; max-width: 70px; overflow-y: auto; max-height: 500px;">
                    @foreach($allMedia as $i => $media)
                    <div class="thumb-item cursor-pointer border-2 {{ $i === 0 ? 'border-primary-500' : 'border-surface-200' }} hover:border-primary-500 transition-colors duration-200"
                         style="width: 70px; height: 70px; min-height: 70px; border-radius: 8px; overflow: hidden; flex-shrink: 0;"
                         data-kind="image"
                         data-large="{{ $media->getUrl('large') }}" data-zoom="{{ $media->getUrl() }}">
                        <img src="{{ $media->getUrl('thumb') }}" alt="{{ $product->name }}" loading="lazy" decoding="async" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    @endforeach
                    @foreach($videos as $video)
                    <div class="thumb-item cursor-pointer border-2 border-surface-200 hover:border-primary-500 transition-colors duration-200"
                         style="width: 70px; height: 70px; min-height: 70px; border-radius: 8px; overflow: hidden; flex-shrink: 0; position: relative; background: #111;"
                         data-kind="video"
                         data-video-type="{{ $video['type'] }}"
                         data-video-src="{{ $video['src'] }}">
                        @if($video['thumb'])
                            <img src="{{ $video['thumb'] }}" alt="Video" loading="lazy" decoding="async" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.75;">
                        @endif
                        <div style="position:absolute; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none;">
                            <div style="width:26px; height:26px; border-radius:50%; background:rgba(0,0,0,0.65); display:flex; align-items:center; justify-content:center;">
                                <svg style="width:12px; height:12px; margin-left:2px;" viewBox="0 0 24 24" fill="#fff"><path d="M8 5v14l11-7z"/></svg>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif

                {{-- Main Image with Zoom --}}
                <div style="flex: 1; min-width: 0; position: relative;" id="image-wrapper">
                    <div id="zoom-container" class="bg-surface-50 border border-surface-200 rounded-xl overflow-hidden relative {{ $mainImage ? 'cursor-crosshair' : '' }}" style="aspect-ratio: 1/1;">
                        @if($mainImage)
                            <img id="main-image" src="{{ $mainImage }}" data-zoom="{{ $fullImage ?: $mainImage }}" alt="{{ $product->name }}" decoding="async" fetchpriority="high" class="w-full h-full object-contain p-6">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-32 h-32 text-surface-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        @endif
                        {{-- Video player overlay (hidden by default, shown when a video thumb is clicked) --}}
                        <div id="main-video" class="hidden absolute inset-0 bg-black flex items-center justify-center"></div>

                        {{-- Fullscreen button (top-right) --}}
                        <button type="button" id="fullscreen-btn" title="View fullscreen" aria-label="View image fullscreen"
                            style="position:absolute; top:12px; right:12px; z-index:5; width:38px; height:38px; border:none; border-radius:50%; background:rgba(17,24,39,0.72); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; backdrop-filter:blur(4px); transition:background 0.2s, transform 0.2s;"
                            onmouseover="this.style.background='rgba(17,24,39,0.95)'; this.style.transform='scale(1.08)'"
                            onmouseout="this.style.background='rgba(17,24,39,0.72)'; this.style.transform='scale(1)'">
                            <svg style="width:18px; height:18px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M8 3H5a2 2 0 0 0-2 2v3"></path>
                                <path d="M21 8V5a2 2 0 0 0-2-2h-3"></path>
                                <path d="M3 16v3a2 2 0 0 0 2 2h3"></path>
                                <path d="M16 21h3a2 2 0 0 0 2-2v-3"></path>
                            </svg>
                        </button>
                    </div>
                    {{-- Zoom Result: positioned over the right column --}}
                    @if($mainImage)
                    <div id="zoom-result" class="hidden absolute top-0 bg-white rounded-xl shadow-2xl border border-surface-200 overflow-hidden z-50 bg-no-repeat pointer-events-none" style="left: calc(100% + 1.5rem);"></div>
                    @endif
                    <p class="text-xs text-surface-400 mt-2 italic">N.B: Image may differ with actual product's layout, color, size & dimension.</p>
                </div>
            </div>
        </div>

        <!-- Product Info -->
        <div class="relative z-10">
            @if($product->brand)
                <p class="text-sm text-surface-500 uppercase tracking-wide mb-2">{{ $product->brand->name }}</p>
            @endif
            <h1 class="text-lg md:text-xl font-display mb-2 leading-tight">{{ $product->name }}</h1>

            @if($reviewStats['count'] > 0)
            <a href="#product-tabs" onclick="var t=document.getElementById('tab-review'); if(t){t.click();}" style="display:inline-flex; align-items:center; gap:7px; margin-bottom:12px; text-decoration:none;">
                <span style="display:flex; gap:1px;">@for($i=1;$i<=5;$i++)<svg style="width:15px;height:15px;color:{{ $i <= round($reviewStats['avg']) ? '#f59e0b' : '#d1d5db' }};" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $starPath }}"/></svg>@endfor</span>
                <span style="font-size:0.82rem; color:#6b7280;">{{ number_format($reviewStats['avg'], 1) }} ({{ $reviewStats['count'] }} review{{ $reviewStats['count'] > 1 ? 's' : '' }})</span>
            </a>
            @endif

            @if($product->sku)
                <p class="text-sm text-surface-500 mb-4">SKU: {{ $product->sku }}</p>
            @endif

            <div class="flex items-center gap-4 mb-6">
                <span class="text-xl font-bold text-primary-600">@bdt($product->price)</span>
                @if($product->compare_price)
                    <span class="text-base text-surface-400 line-through">@bdt($product->compare_price)</span>
                    <span class="bg-accent-100 text-accent-700 text-sm font-semibold px-3 py-1 rounded-full">
                        Save {{ round(($product->compare_price - $product->price) / $product->compare_price * 100) }}%
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-2 mb-6">
                <span class="inline-flex items-center gap-1 text-sm font-medium {{ $product->in_stock ? 'text-primary-600' : 'text-accent-500' }}">
                    <span class="w-2 h-2 rounded-full {{ $product->in_stock ? 'bg-primary-500' : 'bg-accent-500' }}"></span>
                    {{ $product->in_stock ? 'In Stock ('.$product->stock_quantity.' available)' : 'Out of Stock' }}
                </span>
            </div>

            @if($product->short_description)
                <div class="text-surface-600 mb-6 prose prose-sm">{!! $product->short_description !!}</div>
            @endif

            {{-- Quantity + Action Buttons --}}
            @php
                $waNumber = preg_replace('/[^0-9]/', '', \App\Services\SettingService::get('contact_whatsapp', ''));
                $phoneNumber = \App\Services\SettingService::get('contact_phone', '');
                $waMessage   = urlencode('Hi, I want to order: ' . $product->name . ' (Price: ' . number_format($product->price, 0) . '৳)');
            @endphp
            <div x-data="{ qty: 1 }" style="margin-bottom:24px;">
                {{-- Quantity --}}
                <div style="display:flex; align-items:center; gap:16px; margin-bottom:16px;">
                    <span style="font-size:0.9rem; font-weight:600; color:#374151;">Quantity:</span>
                    <div style="display:inline-flex; align-items:center; border:1.5px solid #d1d5db; border-radius:8px; overflow:hidden;">
                        <button type="button" @click="qty = Math.max(1, qty - 1)" aria-label="Decrease quantity"
                            style="width:44px; height:44px; background:#f9fafb; border:none; font-size:1.25rem; font-weight:600; color:#374151; cursor:pointer; display:flex; align-items:center; justify-content:center;"
                            onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f9fafb'">−</button>
                        <span x-text="qty" style="min-width:48px; text-align:center; font-size:1rem; font-weight:700; color:#111827; border-left:1.5px solid #d1d5db; border-right:1.5px solid #d1d5db; height:44px; line-height:44px;"></span>
                        <button type="button" @click="qty++" aria-label="Increase quantity"
                            style="width:44px; height:44px; background:#f9fafb; border:none; font-size:1.25rem; font-weight:600; color:#374151; cursor:pointer; display:flex; align-items:center; justify-content:center;"
                            onmouseover="this.style.background='#f3f4f6'" onmouseout="this.style.background='#f9fafb'">+</button>
                    </div>
                </div>

                {{-- Action buttons — single 2×2 grid so all four are equal height --}}
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; grid-auto-rows:1fr;">
                    <button type="button"
                        @click="addToCart({{ $product->id }}, qty)"
                        @if(!$product->in_stock) disabled @endif
                        class="btn-cart pd-action-btn"
                        style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:13px 10px; background:{{ $product->in_stock ? $tc['color_btn_cart_bg'] : '#9ca3af' }}; color:{{ $tc['color_btn_cart_text'] }}; font-size:0.88rem; font-weight:700; border:none; border-radius:8px; cursor:{{ $product->in_stock ? 'pointer' : 'not-allowed' }}; transition:background 0.2s, color 0.2s;"
                        onmouseover="if(!this.disabled){this.style.background='{{ $tc['color_btn_cart_hover_bg'] }}';this.style.color='{{ $tc['color_btn_cart_hover_text'] }}'}"
                        onmouseout="if(!this.disabled){this.style.background='{{ $tc['color_btn_cart_bg'] }}';this.style.color='{{ $tc['color_btn_cart_text'] }}'}">
                        <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        ADD TO CART
                    </button>
                    <button type="button"
                        @click="buyNow({{ $product->id }}, qty)"
                        @if(!$product->in_stock) disabled @endif
                        class="btn-buy pd-action-btn"
                        style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:13px 10px; background:{{ $product->in_stock ? $tc['color_btn_buy_bg'] : '#9ca3af' }}; color:{{ $tc['color_btn_buy_text'] }}; font-size:0.88rem; font-weight:700; border:none; border-radius:8px; cursor:{{ $product->in_stock ? 'pointer' : 'not-allowed' }}; transition:background 0.2s, color 0.2s;"
                        onmouseover="if(!this.disabled){this.style.background='{{ $tc['color_btn_buy_hover_bg'] }}';this.style.color='{{ $tc['color_btn_buy_hover_text'] }}'}"
                        onmouseout="if(!this.disabled){this.style.background='{{ $tc['color_btn_buy_bg'] }}';this.style.color='{{ $tc['color_btn_buy_text'] }}'}">
                        <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        BUY NOW
                    </button>
                    <a href="{{ $waNumber ? 'https://wa.me/'.$waNumber.'?text='.$waMessage : '#' }}"
                        @if(!$waNumber) onclick="return false;" @else target="_blank" rel="noopener" @endif
                        class="btn-wa pd-action-btn"
                        style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:13px 10px; background:{{ $tc['color_btn_wa_bg'] }}; color:{{ $tc['color_btn_wa_text'] }}; font-size:0.88rem; font-weight:700; text-decoration:none; border-radius:8px; transition:background 0.2s, color 0.2s; {{ !$waNumber ? 'opacity:0.5; cursor:not-allowed;' : '' }}"
                        onmouseover="this.style.background='{{ $tc['color_btn_wa_hover_bg'] }}';this.style.color='{{ $tc['color_btn_wa_hover_text'] }}'"
                        onmouseout="this.style.background='{{ $tc['color_btn_wa_bg'] }}';this.style.color='{{ $tc['color_btn_wa_text'] }}'">
                        <svg style="width:17px;height:17px;flex-shrink:0;" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                        Order On WhatsApp
                    </a>
                    <a href="{{ $phoneNumber ? 'tel:'.preg_replace('/\s+/', '', $phoneNumber) : '#' }}"
                        @if(!$phoneNumber) onclick="return false;" @endif
                        class="btn-call pd-action-btn"
                        style="display:inline-flex; align-items:center; justify-content:center; gap:8px; padding:13px 10px; background:{{ $tc['color_btn_call_bg'] }}; color:{{ $tc['color_btn_call_text'] }}; font-size:0.88rem; font-weight:700; text-decoration:none; border-radius:8px; transition:background 0.2s, color 0.2s; {{ !$phoneNumber ? 'opacity:0.5; cursor:not-allowed;' : '' }}"
                        onmouseover="this.style.background='{{ $tc['color_btn_call_hover_bg'] }}';this.style.color='{{ $tc['color_btn_call_hover_text'] }}'"
                        onmouseout="this.style.background='{{ $tc['color_btn_call_bg'] }}';this.style.color='{{ $tc['color_btn_call_text'] }}'">
                        <svg style="width:16px;height:16px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.948V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 7V5z"/></svg>
                        Call For Order
                    </a>
                </div>
            </div>

            @if($product->warranty_info)
            <div style="border-top:1px solid #e5e7eb; padding-top:16px; margin-top:16px;">
                <p style="font-size:0.875rem; color:#374151;"><strong>Warranty:</strong> {{ $product->warranty_info }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Tabbed Section — fixed tabs: Description (the General-tab description), Q&A, Reviews.
         Custom Tabs (Ingredients, Specifications, Size Chart, How to Use…) slot in after Description. -->
    @php
        $customTabs = collect($product->custom_tabs ?: [])
            ->filter(fn ($t) => ! empty($t['title']) && ! empty($t['content']))
            ->values();

        $hasFaqs = collect($product->faqs ?: [])->filter(fn ($f) => ! empty($f['question']))->count() > 0;

        $tabs = [];
        if (filled($product->description))  $tabs[] = ['key' => 'description', 'label' => 'Description'];
        foreach ($customTabs as $ci => $ct) $tabs[] = ['key' => 'custom-' . $ci, 'label' => $ct['title']];
        if ($hasFaqs)                       $tabs[] = ['key' => 'faq', 'label' => 'FAQ'];
        $tabs[] = ['key' => 'qa',     'label' => 'Q&A'];
        $tabs[] = ['key' => 'review', 'label' => 'Reviews'];

        $defaultTab = $tabs[0]['key'] ?? 'qa';
    @endphp
    <div id="product-tabs" x-data="{ activeTab: '{{ (session('question_success') || old('question')) ? 'qa' : $defaultTab }}' }" style="margin-top:48px;">
        <style>
            /* Product action buttons (Add to Cart / Buy Now / WhatsApp / Call) — compact, equal height */
            .pd-action-btn { padding: 11px 12px !important; line-height: 1.2; text-align: center; }
            .prod-tabs { scrollbar-width: none; -ms-overflow-style: none; }
            .prod-tabs::-webkit-scrollbar { display: none; }
            .prod-tabs > button { flex-shrink: 0; white-space: nowrap; }

            /* ── Product page: mobile tuning ── */
            @media (max-width: 640px) {
                /* Gallery: main image on top, thumbnails in a horizontal scroll strip below */
                .pd-gallery   { flex-direction: column-reverse !important; }
                #thumb-strip  { flex-direction: row !important; width: 100% !important; min-width: 0 !important;
                                max-width: 100% !important; max-height: none !important;
                                overflow-x: auto !important; overflow-y: hidden !important; scrollbar-width: none; }
                #thumb-strip::-webkit-scrollbar { display: none; }
                /* Tabs: tighter padding so more fit before scrolling */
                .prod-tabs > button { padding: 10px 16px !important; font-size: 0.82rem !important; }
                /* Specs table: tighter cells + readable column split */
                .pd-spec td               { padding: 11px 14px !important; font-size: 0.82rem !important; }
                .pd-spec td:first-child   { width: 40% !important; }
                /* Action buttons: tighter padding/font so the 2×2 grid never overflows */
                .pd-action-btn { padding: 10px 8px !important; font-size: 0.82rem !important; }
            }
        </style>
        <!-- Tab Headers (scroll horizontally on narrow screens) -->
        <div class="prod-tabs" style="display:flex; border-bottom:1px solid #d1d5db; overflow-x:auto;">
            @foreach($tabs as $tab)
            <button id="tab-{{ $tab['key'] }}" @click="activeTab = '{{ $tab['key'] }}'"
                :style="'padding:12px 28px; font-size:0.925rem; font-weight:600; border:1px solid; border-bottom:none; cursor:pointer; margin-bottom:-1px; margin-right:-1px;' + (activeTab === '{{ $tab['key'] }}' ? 'background:#111827; color:#fff; border-color:#111827;' : 'background:transparent; color:#374151; border-color:#d1d5db;')">
                {{ $tab['label'] }}
            </button>
            @endforeach
        </div>

        <!-- Tab Content -->
        <div style="border:1px solid #d1d5db; border-top:none; background:#fff;">

            <!-- Description Tab (fixed — the product's main description from the General tab) -->
            @if(filled($product->description))
            <div x-show="activeTab === 'description'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div style="padding:24px;" class="prose prose-sm sm:prose-base lg:prose-lg max-w-none">{!! $product->description !!}</div>
            </div>
            @endif

            <!-- Custom Tabs (admin-defined per product: Specifications, Ingredients, Size Chart, How to Use…) -->
            @foreach($customTabs as $ci => $ct)
            <div x-show="activeTab === 'custom-{{ $ci }}'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display:none;">
                <div style="padding:24px;" class="prose prose-sm sm:prose-base lg:prose-lg max-w-none">{!! $ct['content'] !!}</div>
            </div>
            @endforeach

            <!-- FAQ Tab -->
            <div x-show="activeTab === 'faq'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display:none;">
                @if(!empty($product->faqs) && count($product->faqs))
                <div style="padding:6px 0;">
                    @foreach($product->faqs as $faq)
                        @continue(empty($faq['question']))
                        <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }" style="border-bottom:1px solid #f3f4f6;">
                            <button @click="open = !open" :aria-expanded="open" style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:14px; padding:16px 24px; background:none; border:none; cursor:pointer; text-align:left;">
                                <span style="font-size:0.95rem; font-weight:600; color:#111827; line-height:1.5;">{{ $faq['question'] }}</span>
                                <svg style="width:18px; height:18px; color:#9ca3af; flex-shrink:0; transition:transform 0.25s;" :style="{ transform: open ? 'rotate(180deg)' : 'rotate(0deg)' }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            <div x-show="open" x-collapse>
                                <div style="padding:0 24px 18px; font-size:0.9rem; color:#4b5563; line-height:1.75;">{!! nl2br(e($faq['answer'] ?? '')) !!}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @else
                <div style="padding:40px; text-align:center;">
                    <div style="width:64px; height:64px; background:#f0fdf4; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 16px;">
                        <svg style="width:32px; height:32px; color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <h3 style="font-size:1.1rem; font-weight:600; color:#111827; margin-bottom:6px;">Frequently Asked Questions</h3>
                    <p style="color:#6b7280; font-size:0.9rem;">No FAQs have been added for this product yet.</p>
                </div>
                @endif
            </div>

            <!-- Q&A Tab (public questions → admin answers) -->
            <div x-show="activeTab === 'qa'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display:none;">
                <div style="padding:24px;">
                    @if(session('question_success'))
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#15803d; border-radius:8px; padding:12px 16px; margin-bottom:18px; font-size:0.88rem;">{{ session('question_success') }}</div>
                    @endif

                    @forelse($product->publishedQuestions as $q)
                    <div style="border-bottom:1px solid #f3f4f6; padding:16px 0;">
                        <div style="display:flex; gap:10px;">
                            <span style="flex-shrink:0; width:24px; height:24px; border-radius:50%; background:#eef2ff; color:#4f46e5; font-weight:800; font-size:0.78rem; display:flex; align-items:center; justify-content:center;">Q</span>
                            <div>
                                <p style="margin:0; font-weight:600; color:#111827; font-size:0.92rem; line-height:1.5;">{{ $q->question }}</p>
                                <p style="margin:3px 0 0; font-size:0.74rem; color:#9ca3af;">Asked by {{ $q->name }}{{ $q->answered_at ? ' · ' . $q->answered_at->format('d M Y') : '' }}</p>
                            </div>
                        </div>
                        <div style="display:flex; gap:10px; margin-top:10px;">
                            <span style="flex-shrink:0; width:24px; height:24px; border-radius:50%; background:#f0fdf4; color:#16a34a; font-weight:800; font-size:0.78rem; display:flex; align-items:center; justify-content:center;">A</span>
                            <p style="margin:0; color:#4b5563; font-size:0.9rem; line-height:1.7;">{!! nl2br(e($q->answer)) !!}</p>
                        </div>
                    </div>
                    @empty
                    <p style="color:#6b7280; font-size:0.9rem; margin:0 0 18px;">No questions answered yet. Ask the first one below!</p>
                    @endforelse

                    <div style="margin-top:24px; background:#f9fafb; border:1px solid #eef0f2; border-radius:12px; padding:20px;">
                        <h4 style="margin:0 0 4px; font-size:1rem; font-weight:700; color:#111827;">Have a question about this product?</h4>
                        <p style="margin:0 0 14px; font-size:0.82rem; color:#6b7280;">Ask away — our team will answer it here.</p>
                        @if($errors->any())
                        <div style="background:#fef2f2; border:1px solid #fecaca; color:#dc2626; border-radius:8px; padding:10px 14px; margin-bottom:12px; font-size:0.82rem;">
                            @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                        </div>
                        @endif
                        <form method="POST" action="{{ route('products.questions.store', $product) }}">
                            @csrf
                            <input type="text" name="name" required maxlength="100" placeholder="Your name"
                                value="{{ old('name', auth()->user()->name ?? '') }}"
                                style="width:100%; padding:11px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:0.9rem; outline:none; box-sizing:border-box; margin-bottom:12px;"
                                onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#e2e8f0'">
                            <textarea name="question" required maxlength="1000" rows="3" placeholder="Type your question…"
                                style="width:100%; padding:11px 14px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:0.9rem; outline:none; resize:vertical; box-sizing:border-box; margin-bottom:12px;"
                                onfocus="this.style.borderColor='#f97316'" onblur="this.style.borderColor='#e2e8f0'">{{ old('question') }}</textarea>
                            <button type="submit" style="padding:11px 24px; background:#f97316; color:#fff; border:none; border-radius:8px; font-weight:700; font-size:0.85rem; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#ea6c0a'" onmouseout="this.style.background='#f97316'">Submit question</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Review Tab -->
            <div x-show="activeTab === 'review'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" style="display:none;">
                @php
                    $rvUser      = auth()->user();
                    $rvPurchased = $product->purchasedBy($rvUser);
                    $rvReviewed  = $product->reviewedBy($rvUser);
                    $rvInput     = 'width:100%; padding:10px 12px; border:1.5px solid #e5e7eb; border-radius:8px; font-size:0.9rem; outline:none; box-sizing:border-box; margin-bottom:12px;';
                @endphp
                <div style="padding:24px;">

                    @if(session('review_submitted'))
                    <div style="background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:0.9rem;">{{ session('review_submitted') }}</div>
                    @endif
                    @if(session('review_error'))
                    <div style="background:#fef2f2; border:1px solid #fecaca; color:#991b1b; padding:12px 16px; border-radius:10px; margin-bottom:20px; font-size:0.9rem;">{{ session('review_error') }}</div>
                    @endif

                    {{-- Rating summary --}}
                    @if($reviewStats['count'] > 0)
                    <div style="display:flex; align-items:center; gap:16px; margin-bottom:18px; flex-wrap:wrap;">
                        <div style="font-size:2.4rem; font-weight:800; color:#111827; line-height:1;">{{ number_format($reviewStats['avg'], 1) }}</div>
                        <div>
                            <div style="display:flex; gap:2px;">@for($i=1;$i<=5;$i++)<svg style="width:18px;height:18px;color:{{ $i <= round($reviewStats['avg']) ? '#f59e0b' : '#d1d5db' }};" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $starPath }}"/></svg>@endfor</div>
                            <div style="font-size:0.85rem; color:#6b7280; margin-top:4px;">Based on {{ $reviewStats['count'] }} review{{ $reviewStats['count'] > 1 ? 's' : '' }}</div>
                        </div>
                    </div>
                    @endif

                    {{-- Approved reviews --}}
                    @forelse($product->approvedReviews as $review)
                    <div style="border-top:1px solid #f3f4f6; padding:16px 0;">
                        <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px;">
                            <div style="display:flex; align-items:center; gap:10px;">
                                <span style="width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,#16a34a,#22c55e);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:0.9rem;flex-shrink:0;">{{ strtoupper(substr($review->name,0,1)) }}</span>
                                <div>
                                    <div style="font-weight:600; font-size:0.9rem; color:#111827;">{{ $review->name }}@if($review->is_verified)<span style="color:#16a34a; font-size:0.7rem; font-weight:600; margin-left:6px;">✓ Verified Purchase</span>@endif</div>
                                    <div style="display:flex; gap:1px; margin-top:2px;">@for($i=1;$i<=5;$i++)<svg style="width:13px;height:13px;color:{{ $i <= $review->rating ? '#f59e0b' : '#d1d5db' }};" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $starPath }}"/></svg>@endfor</div>
                                </div>
                            </div>
                            <span style="font-size:0.75rem; color:#9ca3af; white-space:nowrap;">{{ $review->created_at->format('M d, Y') }}</span>
                        </div>
                        @if($review->title)<p style="font-weight:600; font-size:0.9rem; color:#111827; margin:10px 0 4px;">{{ $review->title }}</p>@endif
                        <p style="font-size:0.88rem; color:#4b5563; line-height:1.7; margin:6px 0 0;">{{ $review->comment }}</p>
                    </div>
                    @empty
                    <p style="color:#6b7280; font-size:0.9rem; padding:8px 0 16px;">No reviews yet — only verified buyers can review, so check back soon.</p>
                    @endforelse

                    {{-- Submission / eligibility (only logged-in buyers can review) --}}
                    <div style="margin-top:24px; border-top:1px solid #e5e7eb; padding-top:22px;">
                        @guest
                            <div style="background:#f9fafb; border:1px solid #e5e7eb; border-radius:10px; padding:16px 18px; font-size:0.9rem; color:#4b5563;">
                                Please <a href="{{ route('login') }}" style="color:#16a34a; font-weight:700; text-decoration:none;">log in</a> to write a review. Only customers who purchased this product can review it.
                            </div>
                        @else
                            @if($rvReviewed)
                                <div style="background:#f0fdf4; border:1px solid #bbf7d0; border-radius:10px; padding:16px 18px; font-size:0.9rem; color:#166534;">You've already reviewed this product. Thank you!</div>
                            @elseif(! $rvPurchased)
                                <div style="background:#fffbeb; border:1px solid #fde68a; border-radius:10px; padding:16px 18px; font-size:0.9rem; color:#92400e;">Only verified buyers can review this product. Once your order is delivered, you'll be able to share your experience here.</div>
                            @else
                                <h3 style="font-size:1.05rem; font-weight:700; color:#111827; margin:0 0 14px;">Write a Review</h3>
                                <form method="POST" action="{{ route('products.reviews.store', $product) }}" x-data="{ rating: {{ (int) old('rating', 0) }}, hover: 0 }">
                                    @csrf
                                    <div style="margin-bottom:14px;">
                                        <label style="display:block; font-size:0.82rem; font-weight:600; color:#374151; margin-bottom:6px;">Your rating *</label>
                                        <div style="display:flex; gap:4px;">
                                            @for($i=1;$i<=5;$i++)
                                            <button type="button" @click="rating = {{ $i }}" @mouseenter="hover = {{ $i }}" @mouseleave="hover = 0" aria-label="Rate {{ $i }} star{{ $i>1?'s':'' }}" style="background:none; border:none; cursor:pointer; padding:0; line-height:0;">
                                                <svg style="width:30px;height:30px;" :style="{ color: ({{ $i }} <= (hover || rating)) ? '#f59e0b' : '#d1d5db' }" fill="currentColor" viewBox="0 0 20 20"><path d="{{ $starPath }}"/></svg>
                                            </button>
                                            @endfor
                                        </div>
                                        <input type="hidden" name="rating" :value="rating">
                                        @error('rating')<p style="color:#ef4444; font-size:0.78rem; margin-top:4px;">{{ $message }}</p>@enderror
                                    </div>
                                    <input type="text" name="title" value="{{ old('title') }}" placeholder="Review title (optional)" maxlength="150" style="{{ $rvInput }}">
                                    <textarea name="comment" rows="4" required maxlength="2000" placeholder="Share your experience with this product... *" style="{{ $rvInput }} resize:vertical;">{{ old('comment') }}</textarea>
                                    @error('comment')<p style="color:#ef4444; font-size:0.78rem; margin:-6px 0 10px;">{{ $message }}</p>@enderror
                                    <button type="submit" style="display:inline-flex; align-items:center; gap:8px; padding:11px 26px; background:#111827; color:#fff; font-size:0.88rem; font-weight:700; border:none; border-radius:8px; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#000'" onmouseout="this.style.background='#111827'">Submit Review</button>
                                </form>
                            @endif
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count())
    <div style="margin-top:48px; border-top:1px solid #e5e7eb; padding-top:32px;">
        <h2 class="text-2xl font-display mb-6">Related Products</h2>
        <div class="product-grid grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 items-start">
            @foreach($relatedProducts as $related)
                @include('components.product-card', ['product' => $related])
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Fullscreen gallery lightbox --}}
<div id="fs-modal" style="display:none; position:fixed; inset:0; z-index:9999; background:rgba(0,0,0,0.94); flex-direction:column;">
    {{-- Header with counter + close --}}
    <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 24px; color:#fff;">
        <div id="fs-counter" style="font-size:0.875rem; font-weight:500; color:rgba(255,255,255,0.8);"></div>
        <button type="button" id="fs-close" title="Close (Esc)" aria-label="Close fullscreen"
            style="width:44px; height:44px; border:none; border-radius:50%; background:rgba(255,255,255,0.12); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s;"
            onmouseover="this.style.background='rgba(255,255,255,0.25)'"
            onmouseout="this.style.background='rgba(255,255,255,0.12)'">
            <svg style="width:22px; height:22px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"/></svg>
        </button>
    </div>

    {{-- Main stage with prev/next arrows --}}
    <div style="flex:1 1 0; min-height:0; position:relative; display:flex; align-items:center; justify-content:center; padding:0 70px; overflow:hidden;">
        <button type="button" id="fs-prev" title="Previous (←)" aria-label="Previous image"
            style="position:absolute; left:16px; top:50%; transform:translateY(-50%); width:48px; height:48px; border:none; border-radius:50%; background:rgba(255,255,255,0.12); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s;"
            onmouseover="this.style.background='rgba(255,255,255,0.28)'"
            onmouseout="this.style.background='rgba(255,255,255,0.12)'">
            <svg style="width:24px; height:24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M15 18l-6-6 6-6"/></svg>
        </button>
        <div id="fs-content" style="width:100%; height:100%; max-width:1400px; display:flex; align-items:center; justify-content:center;"></div>
        <button type="button" id="fs-next" title="Next (→)" aria-label="Next image"
            style="position:absolute; right:16px; top:50%; transform:translateY(-50%); width:48px; height:48px; border:none; border-radius:50%; background:rgba(255,255,255,0.12); color:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background 0.2s;"
            onmouseover="this.style.background='rgba(255,255,255,0.28)'"
            onmouseout="this.style.background='rgba(255,255,255,0.12)'">
            <svg style="width:24px; height:24px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M9 18l6-6-6-6"/></svg>
        </button>
    </div>

    {{-- Thumbnail strip at bottom --}}
    <div id="fs-thumbs" style="display:flex; gap:8px; padding:16px 24px; overflow-x:auto; justify-content:center; flex-wrap:nowrap;"></div>
</div>
@endsection

@push('scripts')
<script>
// Buy Now: add to cart (with qty) then redirect to checkout
function buyNow(productId, qty) {
    var btn = document.querySelector('.btn-buy');
    window.orderNow(productId, btn, qty);
}

document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('zoom-container');
    const mainImg = document.getElementById('main-image');
    const result = document.getElementById('zoom-result');

    if (!container || !mainImg || !result) return;

    const ZOOM_FACTOR = 3;
    let zoomImg = new Image();
    let zoomReady = false;
    let isZooming = false;
    let videoMode = false; // disable zoom while a video is showing
    // Track what's currently shown in the main gallery so the fullscreen button knows what to open
    let currentView = { kind: 'image', src: mainImg ? (mainImg.dataset.zoom || mainImg.src) : null, videoType: null };

    function preloadZoom() {
        const src = mainImg.dataset.zoom || mainImg.src;
        zoomReady = false;
        zoomImg = new Image();
        zoomImg.onload = function () { zoomReady = true; };
        zoomImg.src = src;
    }
    preloadZoom();

    // Size the zoom result to match the container height
    function sizeResult() {
        const h = container.offsetHeight;
        result.style.width = h + 'px';
        result.style.height = h + 'px';
    }

    container.addEventListener('mouseenter', function () {
        if (videoMode || !zoomReady || window.innerWidth < 1024) return;
        isZooming = true;
        sizeResult();
        result.classList.remove('hidden');

        const src = mainImg.dataset.zoom || mainImg.src;
        result.style.backgroundImage = 'url(' + src + ')';
        result.style.backgroundSize = (container.offsetWidth * ZOOM_FACTOR) + 'px ' + (container.offsetHeight * ZOOM_FACTOR) + 'px';
    });

    container.addEventListener('mouseleave', function () {
        isZooming = false;
        result.classList.add('hidden');
    });

    container.addEventListener('mousemove', function (e) {
        if (videoMode || !isZooming || !zoomReady) return;

        const rect = container.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;

        // Percentage position of cursor within container
        const percX = x / rect.width;
        const percY = y / rect.height;

        const bgW = rect.width * ZOOM_FACTOR;
        const bgH = rect.height * ZOOM_FACTOR;
        const resW = result.offsetWidth;
        const resH = result.offsetHeight;

        // Center the zoom on cursor position
        const bgX = Math.max(0, Math.min(percX * bgW - resW / 2, bgW - resW));
        const bgY = Math.max(0, Math.min(percY * bgH - resH / 2, bgH - resH));

        result.style.backgroundPosition = -bgX + 'px ' + -bgY + 'px';
    });

    // Thumbnail switching (images + videos)
    const mainVideo = document.getElementById('main-video');

    function showImage() {
        if (mainVideo) {
            mainVideo.innerHTML = '';
            mainVideo.classList.add('hidden');
            mainVideo.classList.remove('flex');
        }
        if (mainImg) mainImg.classList.remove('hidden');
        videoMode = false;
        container.classList.add('cursor-crosshair');
        if (mainImg) {
            currentView = { kind: 'image', src: mainImg.dataset.zoom || mainImg.src, videoType: null };
        }
    }

    function showVideo(type, src) {
        if (!mainVideo) return;
        if (mainImg) mainImg.classList.add('hidden');
        // Disable zoom/magnify while video is showing
        videoMode = true;
        isZooming = false;
        result.classList.add('hidden');
        container.classList.remove('cursor-crosshair');
        currentView = { kind: 'video', src: src, videoType: type };
        let html = '';
        if (type === 'youtube' || type === 'youtube_reel') {
            // Stealth-wrap the YouTube iframe: overflow:hidden container + over-scaled iframe
            // so the YouTube logo and UI corners fall outside the visible area.
            html = '<div style="position:absolute; inset:0; overflow:hidden; background:#000;">'
                 +   '<iframe src="' + src + '" frameborder="0" allow="autoplay; encrypted-media; picture-in-picture" loading="lazy"'
                 +     ' style="position:absolute; top:50%; left:50%; width:177.8%; height:100%; transform:translate(-50%,-50%) scale(1.22); pointer-events:none; border:0;"></iframe>'
                 +   '<div style="position:absolute; inset:0; z-index:1;"></div>'
                 + '</div>';
        } else {
            html = '<video src="' + src + '" autoplay muted loop playsinline style="width:100%; height:100%; object-fit:contain; background:#000;"></video>';
        }
        mainVideo.innerHTML = html;
        mainVideo.classList.remove('hidden');
        mainVideo.classList.add('flex');
    }

    document.querySelectorAll('.thumb-item').forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            document.querySelectorAll('.thumb-item').forEach(function (t) {
                t.classList.remove('border-primary-500');
                t.classList.add('border-surface-200');
            });
            this.classList.remove('border-surface-200');
            this.classList.add('border-primary-500');

            if (this.dataset.kind === 'video') {
                showVideo(this.dataset.videoType, this.dataset.videoSrc);
            } else {
                showImage();
                if (mainImg) {
                    mainImg.src = this.dataset.large;
                    mainImg.dataset.zoom = this.dataset.zoom;
                    preloadZoom();
                }
            }
        });
    });

    // ── Fullscreen gallery lightbox ──────────────────────────────────────
    const fsBtn     = document.getElementById('fullscreen-btn');
    const fsModal   = document.getElementById('fs-modal');
    const fsClose   = document.getElementById('fs-close');
    const fsContent = document.getElementById('fs-content');
    const fsPrev    = document.getElementById('fs-prev');
    const fsNext    = document.getElementById('fs-next');
    const fsThumbs  = document.getElementById('fs-thumbs');
    const fsCounter = document.getElementById('fs-counter');

    // Build the gallery items array by reading the existing .thumb-item elements
    const galleryItems = [];
    document.querySelectorAll('.thumb-item').forEach(function (el) {
        if (el.dataset.kind === 'video') {
            galleryItems.push({
                kind: 'video',
                videoType: el.dataset.videoType,
                src: el.dataset.videoSrc,
                thumb: el.querySelector('img') ? el.querySelector('img').src : null,
            });
        } else {
            galleryItems.push({
                kind: 'image',
                src: el.dataset.large,
                zoom: el.dataset.zoom,
                thumb: el.querySelector('img') ? el.querySelector('img').src : null,
            });
        }
    });
    // Fallback: if there are no thumbs at all but there's a main image, create a single-item gallery
    if (!galleryItems.length && mainImg) {
        galleryItems.push({ kind: 'image', src: mainImg.dataset.zoom || mainImg.src, zoom: mainImg.dataset.zoom || mainImg.src, thumb: mainImg.src });
    }

    let fsIndex = 0;

    function renderFsItem() {
        if (!fsContent || !galleryItems.length) return;
        const item = galleryItems[fsIndex];
        let html = '';
        if (item.kind === 'image') {
            html = '<img src="' + (item.zoom || item.src) + '" alt="" style="max-width:100%; max-height:100%; object-fit:contain; display:block; margin:auto;">';
        } else if (item.kind === 'video') {
            if (item.videoType === 'youtube' || item.videoType === 'youtube_reel') {
                // Extract YouTube video ID from the embed URL so we can rebuild a stealth-params URL
                const base = (item.src || '').split('?')[0];
                const idMatch = base.match(/\/embed\/([\w-]{11})/);
                const vid = idMatch ? idMatch[1] : '';
                const fsSrc = base + '?autoplay=1&mute=1&loop=1&playlist=' + vid
                    + '&controls=0&showinfo=0&modestbranding=1&rel=0&playsinline=1'
                    + '&iv_load_policy=3&disablekb=1&fs=0&cc_load_policy=0';
                // Same stealth wrapper as the main gallery — overflow:hidden + over-scaled iframe
                // crops the YouTube logo and any UI at the corners.
                html = '<div style="position:relative; width:100%; max-width:1400px; aspect-ratio:16/9; max-height:100%; overflow:hidden; background:#000;">'
                     +   '<iframe src="' + fsSrc + '" frameborder="0" allow="autoplay; encrypted-media; picture-in-picture" loading="lazy"'
                     +     ' style="position:absolute; top:50%; left:50%; width:100%; height:100%; transform:translate(-50%,-50%) scale(1.22); pointer-events:none; border:0;"></iframe>'
                     +   '<div style="position:absolute; inset:0; z-index:1;"></div>'
                     + '</div>';
            } else {
                html = '<video src="' + (item.src || '') + '" autoplay muted loop playsinline style="max-width:100%; max-height:100%; display:block; margin:auto; background:#000;"></video>';
            }
        }
        fsContent.innerHTML = html;
        if (fsCounter) fsCounter.textContent = (fsIndex + 1) + ' / ' + galleryItems.length;
        // Highlight active thumbnail
        if (fsThumbs) {
            Array.from(fsThumbs.children).forEach(function (t, i) {
                t.style.borderColor = (i === fsIndex) ? '#fff' : 'rgba(255,255,255,0.2)';
                t.style.opacity = (i === fsIndex) ? '1' : '0.55';
            });
        }
        // Toggle prev/next visibility when only one item
        if (fsPrev) fsPrev.style.display = galleryItems.length > 1 ? 'flex' : 'none';
        if (fsNext) fsNext.style.display = galleryItems.length > 1 ? 'flex' : 'none';
    }

    function buildFsThumbs() {
        if (!fsThumbs) return;
        fsThumbs.innerHTML = '';
        if (galleryItems.length <= 1) { fsThumbs.style.display = 'none'; return; }
        fsThumbs.style.display = 'flex';
        galleryItems.forEach(function (item, i) {
            const t = document.createElement('div');
            t.style.cssText = 'width:64px; height:64px; border-radius:6px; overflow:hidden; flex-shrink:0; cursor:pointer; border:2px solid rgba(255,255,255,0.2); position:relative; background:#111; transition:opacity 0.2s, border-color 0.2s;';
            if (item.thumb) {
                const img = document.createElement('img');
                img.src = item.thumb;
                img.style.cssText = 'width:100%; height:100%; object-fit:cover;';
                t.appendChild(img);
            }
            if (item.kind === 'video') {
                const play = document.createElement('div');
                play.style.cssText = 'position:absolute; inset:0; display:flex; align-items:center; justify-content:center; pointer-events:none;';
                play.innerHTML = '<div style="width:22px; height:22px; border-radius:50%; background:rgba(0,0,0,0.65); display:flex; align-items:center; justify-content:center;"><svg style="width:10px; height:10px; margin-left:2px;" viewBox="0 0 24 24" fill="#fff"><path d="M8 5v14l11-7z"/></svg></div>';
                t.appendChild(play);
            }
            t.addEventListener('click', function () { fsIndex = i; renderFsItem(); });
            fsThumbs.appendChild(t);
        });
    }

    function findCurrentIndex() {
        // Try to match the currently shown item to a gallery index
        if (currentView.kind === 'image' && currentView.src) {
            const idx = galleryItems.findIndex(function (it) {
                return it.kind === 'image' && (it.zoom === currentView.src || it.src === currentView.src);
            });
            if (idx !== -1) return idx;
        }
        if (currentView.kind === 'video' && currentView.src) {
            const idx = galleryItems.findIndex(function (it) {
                return it.kind === 'video' && it.src === currentView.src;
            });
            if (idx !== -1) return idx;
        }
        return 0;
    }

    function openFs() {
        if (!fsModal || !galleryItems.length) return;
        fsIndex = findCurrentIndex();
        buildFsThumbs();
        renderFsItem();
        fsModal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeFs() {
        if (!fsModal || !fsContent) return;
        fsContent.innerHTML = ''; // stops any playing video/iframe
        fsModal.style.display = 'none';
        document.body.style.overflow = '';
    }

    function gotoPrev() { fsIndex = (fsIndex - 1 + galleryItems.length) % galleryItems.length; renderFsItem(); }
    function gotoNext() { fsIndex = (fsIndex + 1) % galleryItems.length; renderFsItem(); }

    if (fsBtn)   fsBtn.addEventListener('click', openFs);
    if (fsClose) fsClose.addEventListener('click', closeFs);
    if (fsPrev)  fsPrev.addEventListener('click', gotoPrev);
    if (fsNext)  fsNext.addEventListener('click', gotoNext);
    if (fsModal) {
        fsModal.addEventListener('click', function (e) {
            // Click on backdrop only (not on the content/thumbs/buttons)
            if (e.target === fsModal) closeFs();
        });
    }
    document.addEventListener('keydown', function (e) {
        if (!fsModal || fsModal.style.display !== 'flex') return;
        if (e.key === 'Escape')     closeFs();
        if (e.key === 'ArrowLeft')  gotoPrev();
        if (e.key === 'ArrowRight') gotoNext();
    });
});

// ── Analytics: fire view_item on every product page view ──
window.dsTrack && window.dsTrack('view_item', {
    id:    {{ (int) $product->id }},
    name:  @json($product->name),
    price: {{ (float) $product->price }},
    brand: @json($product->brand?->name),
    category: @json($product->categories->first()?->name),
});
</script>
@endpush
