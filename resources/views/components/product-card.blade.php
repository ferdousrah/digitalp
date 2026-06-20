@props(['product'])
@php
    $tc = \App\Filament\Pages\TemplateSettings::defaults();
    foreach ($tc as $k => $d) { $tc[$k] = \App\Services\SettingService::get($k, $d); }

    $pc = \App\Filament\Pages\ProductCardSettings::defaults();
    foreach ($pc as $k => $d) { $pc[$k] = \App\Services\SettingService::get($k, $d); }

    $isWishlisted = in_array($product->id, $wishlistProductIds ?? []);
    $isCompared   = in_array($product->id, $compareProductIds ?? []);

    // Thumbnail video (optional) — resolves to a usable src + type
    $thumbImage = $product->getFirstMediaUrl('product_thumbnail', 'medium');
    $tv = $product->thumbnail_video ?? null;
    $tvType = $tv['type'] ?? null;
    $tvSrc = null;
    $tvYoutubeId = null;
    if (in_array($tvType, ['youtube', 'youtube_reel']) && !empty($tv['url'])) {
        if (preg_match('~(?:youtube\.com/(?:watch\?v=|shorts/|embed/|v/)|youtu\.be/)([\w-]{11})~', $tv['url'], $m)) {
            $tvYoutubeId = $m[1];
            $tvSrc = 'https://www.youtube.com/embed/' . $m[1] . '?autoplay=1&mute=1&loop=1&playlist=' . $m[1] . '&controls=0&modestbranding=1&rel=0';
        }
    } elseif ($tvType === 'upload' && !empty($tv['file'])) {
        $tvSrc = \Illuminate\Support\Facades\Storage::disk('public')->url($tv['file']);
    }

    // Multiple thumbnails → hover slider (only when there's no video and more than one image)
    $thumbMedia = $product->getMedia('product_thumbnail');
    $hasSlider  = !$tvSrc && $thumbMedia->count() > 1;
    $slides = [];
    if ($hasSlider) {
        foreach ($thumbMedia as $m) {
            $slides[] = [
                'jpg'  => $m->hasGeneratedConversion('medium')      ? $m->getUrl('medium')      : $m->getUrl(),
                'webp' => $m->hasGeneratedConversion('medium_webp') ? $m->getUrl('medium_webp') : null,
            ];
        }
    }
@endphp

{{-- Card radius / image ratio / visibility / hover come from the per-device CSS in
     partials/product-card-css.blade.php. Colours stay inline (global). --}}
<div class="product-card" style="position:relative; background:{{ $pc['pc_card_bg'] }}; overflow:hidden; border:1px solid {{ $pc['pc_card_border'] }};">

    <!-- Image Section -->
    <a href="{{ route('products.show', $product) }}" class="product-card-imglink" style="display:block; position:relative; overflow:hidden;">
        @if($tvSrc && $tvType === 'upload')
            {{-- Uploaded video: autoplay muted loop --}}
            <video class="product-card-img product-card-video"
                src="{{ $tvSrc }}"
                @if($thumbImage) poster="{{ $thumbImage }}" @endif
                autoplay muted loop playsinline preload="auto"
                oncanplay="this.play().catch(()=>{})"
                style="width:100%; height:100%; object-fit:cover; transition:transform 0.6s cubic-bezier(.4,0,.2,1);"></video>
            <div style="position:absolute; top:12px; right:12px; width:28px; height:28px; border-radius:50%; background:rgba(0,0,0,0.55); display:flex; align-items:center; justify-content:center; pointer-events:none; z-index:2;">
                <svg style="width:12px; height:12px; margin-left:2px;" viewBox="0 0 24 24" fill="#fff"><path d="M8 5v14l11-7z"/></svg>
            </div>
        @elseif($tvSrc && $tvYoutubeId)
            <div class="product-card-yt-wrap" style="position:absolute; inset:0; overflow:hidden; background:#000;">
                <iframe
                    src="https://www.youtube-nocookie.com/embed/{{ $tvYoutubeId }}?autoplay=1&mute=1&loop=1&playlist={{ $tvYoutubeId }}&controls=0&showinfo=0&modestbranding=1&rel=0&playsinline=1&iv_load_policy=3&disablekb=1&fs=0&cc_load_policy=0"
                    frameborder="0"
                    allow="autoplay; encrypted-media; picture-in-picture"
                    loading="lazy"
                    style="position:absolute; top:50%; left:50%; width:177.8%; height:100%; transform:translate(-50%,-50%) scale(1.22); pointer-events:none; border:0;"></iframe>
                <div style="position:absolute; inset:0; z-index:1;"></div>
            </div>
        @elseif($hasSlider)
            {{-- Multi-thumbnail slider: no autoplay, prev/next shown on hover --}}
            <div class="pc-slider" data-pc-slider data-index="0" style="position:absolute; inset:0; overflow:hidden;">
                <div class="pc-slider-track" style="display:flex; height:100%; transition:transform 0.35s ease;">
                    @foreach($slides as $s)
                    <div class="pc-slide" style="flex:0 0 100%; height:100%;">
                        <picture>
                            @if($s['webp'])<source srcset="{{ $s['webp'] }}" type="image/webp">@endif
                            <img src="{{ $s['jpg'] }}" alt="{{ $product->name }}" loading="lazy" decoding="async" style="width:100%; height:100%; object-fit:cover;">
                        </picture>
                    </div>
                    @endforeach
                </div>
                <button type="button" class="pc-slider-prev" aria-label="Previous image" onclick="event.preventDefault();event.stopPropagation();pcSlide(this,-1)">&#8249;</button>
                <button type="button" class="pc-slider-next" aria-label="Next image" onclick="event.preventDefault();event.stopPropagation();pcSlide(this,1)">&#8250;</button>
                <div class="pc-slider-dots">
                    @foreach($slides as $i => $s)
                    <span class="pc-slider-dot {{ $i === 0 ? 'active' : '' }}"></span>
                    @endforeach
                </div>
            </div>
        @elseif($thumbImage)
            <x-product-image
                :product="$product"
                size="medium"
                collection="product_thumbnail"
                class="product-card-img"
                style="width:100%; height:100%; object-fit:cover; transition:transform 0.6s cubic-bezier(.4,0,.2,1);" />
        @else
            <div style="width:100%; height:100%; background:linear-gradient(135deg,#f9fafb,#f3f4f6); display:flex; align-items:center; justify-content:center;">
                <svg style="width:64px; height:64px; color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        @endif

        <!-- Badges (CSS hides per device) -->
        <div style="position:absolute; top:12px; left:12px; display:flex; flex-direction:column; gap:6px;">
            @if($product->compare_price)
                <span class="pc-badge-sale" style="background:{{ $pc['pc_sale_badge_bg'] }}; color:{{ $pc['pc_sale_badge_text'] }}; font-size:0.75rem; font-weight:700; padding:4px 10px; border-radius:20px; letter-spacing:0.02em;">
                    -{{ round((($product->compare_price - $product->price) / $product->compare_price) * 100) }}%
                </span>
            @endif
            @if($product->is_featured)
                <span class="pc-badge-featured" style="background:{{ $pc['pc_featured_badge_bg'] }}; color:{{ $pc['pc_featured_badge_text'] }}; font-size:0.7rem; font-weight:700; padding:4px 10px; border-radius:20px; letter-spacing:0.03em; text-transform:uppercase;">Featured</span>
            @endif
        </div>

        <!-- Quick Action Buttons (slide in from right on hover; CSS hides per device) -->
        <div class="product-card-actions" style="position:absolute; top:12px; right:12px; display:flex; flex-direction:column; gap:8px; transform:translateX(60px); opacity:0; transition:all 0.3s cubic-bezier(.4,0,.2,1);">
            <button type="button" title="{{ $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}"
                aria-label="{{ $isWishlisted ? 'Remove from Wishlist' : 'Add to Wishlist' }}"
                aria-pressed="{{ $isWishlisted ? 'true' : 'false' }}"
                class="pc-act-wishlist wishlist-btn-{{ $product->id }} {{ $isWishlisted ? 'wishlisted' : '' }}"
                onclick="event.preventDefault(); event.stopPropagation(); toggleWishlist({{ $product->id }}, this)"
                style="width:36px; height:36px; background:{{ $isWishlisted ? '#ef4444' : '#fff' }}; border:none; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.12); transition:all 0.2s;">
                <svg style="width:18px; height:18px; color:{{ $isWishlisted ? '#fff' : '#6b7280' }}; transition:color 0.2s;" fill="{{ $isWishlisted ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
            </button>

            <button type="button" title="{{ $isCompared ? 'Remove from Compare' : 'Add to Compare' }}"
                aria-label="{{ $isCompared ? 'Remove from Compare' : 'Add to Compare' }}"
                aria-pressed="{{ $isCompared ? 'true' : 'false' }}"
                class="pc-act-compare compare-btn-{{ $product->id }} {{ $isCompared ? 'compared' : '' }}"
                onclick="event.preventDefault(); event.stopPropagation(); toggleCompare({{ $product->id }}, this)"
                style="width:36px; height:36px; background:{{ $isCompared ? '#16a34a' : '#fff' }}; border:none; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.12); transition:all 0.2s;">
                <svg style="width:18px; height:18px; color:{{ $isCompared ? '#fff' : '#6b7280' }}; transition:color 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
            </button>

            <button type="button" title="Quick View" aria-label="Quick View"
                class="pc-act-quickview"
                onclick="event.preventDefault(); event.stopPropagation(); openQuickView('{{ route('products.quickView', $product) }}')"
                style="width:36px; height:36px; background:#fff; border:none; border-radius:50%; display:flex; align-items:center; justify-content:center; cursor:pointer; box-shadow:0 2px 8px rgba(0,0,0,0.12); transition:all 0.2s;"
                onmouseover="this.style.background='#2563eb'; this.querySelector('svg').style.color='#fff'"
                onmouseout="this.style.background='#fff'; this.querySelector('svg').style.color='#6b7280'">
                <svg style="width:18px; height:18px; color:#6b7280; transition:color 0.2s;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
            </button>
        </div>

        <!-- Out of Stock overlay -->
        @unless($product->in_stock)
            <div style="position:absolute; bottom:0; left:0; right:0; background:{{ $pc['pc_oos_bg'] }}; color:{{ $pc['pc_oos_text'] }}; text-align:center; padding:6px; font-size:0.8rem; font-weight:600; letter-spacing:0.05em; text-transform:uppercase;">
                Out of Stock
            </div>
        @endunless
    </a>

    <!-- Content Section -->
    <div style="padding:16px;">

        @if($product->brand)
            <p class="pc-brand" style="font-size:0.7rem; color:{{ $pc['pc_brand_color'] }}; text-transform:uppercase; letter-spacing:0.08em; font-weight:600; margin-bottom:6px;">{{ $product->brand->name }}</p>
        @endif

        <h3 style="font-size:0.925rem; font-weight:600; color:{{ $pc['pc_name_color'] }}; margin-bottom:8px; line-height:1.4; min-height:2.8em; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">
            <a href="{{ route('products.show', $product) }}" style="text-decoration:none; color:inherit; transition:color 0.2s;"
                onmouseover="this.style.color='{{ $pc['pc_name_hover_color'] }}'"
                onmouseout="this.style.color='{{ $pc['pc_name_color'] }}'">{{ $product->name }}</a>
        </h3>

        <div style="display:flex; align-items:baseline; gap:8px; margin-bottom:12px;">
            <span style="font-size:1.15rem; font-weight:700; color:{{ $pc['pc_price_color'] }};">@bdt($product->price)</span>
            @if($product->compare_price)
                <span class="pc-compare-price" style="font-size:0.825rem; color:{{ $pc['pc_compare_price_color'] }}; text-decoration:line-through;">@bdt($product->compare_price)</span>
            @endif
        </div>

        <!-- Hover-reveal action buttons (flex so a hidden one lets the other fill; CSS hides per device) -->
        @if($product->in_stock)
        <div class="product-card-btns"
            style="display:flex; gap:8px; max-height:0; overflow:hidden; opacity:0; transform:translateY(10px); margin-top:0;">

            <button type="button"
                class="pc-btn pc-btn-cart"
                onclick="event.preventDefault(); event.stopPropagation(); addToCart({{ $product->id }}, this)"
                data-product-id="{{ $product->id }}"
                style="flex:1; display:flex; align-items:center; justify-content:center; gap:5px; padding:9px 6px; background:{{ $tc['color_btn_cart_bg'] }}; color:{{ $tc['color_btn_cart_text'] }}; border:none; cursor:pointer; font-size:0.75rem; font-weight:700; letter-spacing:0.04em; text-transform:uppercase; transition:background 0.2s, color 0.2s;"
                onmouseover="this.style.background='{{ $tc['color_btn_cart_hover_bg'] }}';this.style.color='{{ $tc['color_btn_cart_hover_text'] }}'"
                onmouseout="this.style.background='{{ $tc['color_btn_cart_bg'] }}';this.style.color='{{ $tc['color_btn_cart_text'] }}'">
                <i class="fi fi-rr-shopping-cart" style="font-size:12px; line-height:1;"></i>
                <span>Add to Cart</span>
            </button>

            <button type="button"
                class="pc-btn pc-btn-order"
                onclick="event.stopPropagation(); orderNow({{ $product->id }}, this)"
                style="flex:1; display:flex; align-items:center; justify-content:center; gap:5px; padding:9px 6px; background:{{ $tc['color_btn_buy_bg'] }}; color:{{ $tc['color_btn_buy_text'] }}; border:none; font-size:0.75rem; font-weight:700; letter-spacing:0.04em; text-transform:uppercase; cursor:pointer; transition:background 0.2s, color 0.2s;"
                onmouseover="this.style.background='{{ $tc['color_btn_buy_hover_bg'] }}';this.style.color='{{ $tc['color_btn_buy_hover_text'] }}'"
                onmouseout="this.style.background='{{ $tc['color_btn_buy_bg'] }}';this.style.color='{{ $tc['color_btn_buy_text'] }}'">
                <i class="fi fi-rr-bolt" style="font-size:12px; line-height:1;"></i>
                <span>Buy Now</span>
            </button>
        </div>
        @endif

    </div>
</div>

{{-- Card CSS (static + per-device responsive) — emitted once per page --}}
@once
@push('styles')
@include('partials.product-card-css')
@endpush
@endonce

@once
@push('scripts')
<script>
(function () {
    function forcePlay(v) {
        v.muted = true;
        v.playsInline = true;
        const p = v.play();
        if (p && typeof p.catch === 'function') p.catch(() => {});
    }
    function initCardVideos() {
        const videos = document.querySelectorAll('video.product-card-video');
        if (!videos.length) return;
        const io = ('IntersectionObserver' in window) ? new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) forcePlay(e.target);
                else { try { e.target.pause(); } catch (_) {} }
            });
        }, { threshold: 0.1 }) : null;
        videos.forEach(v => {
            forcePlay(v);
            if (io) io.observe(v);
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCardVideos);
    } else {
        initCardVideos();
    }
    document.addEventListener('barba:after', initCardVideos);

    // Product-card thumbnail slider (manual prev/next, no autoplay)
    window.pcSlide = function (btn, dir) {
        var slider = btn.closest('[data-pc-slider]');
        if (!slider) return;
        var track  = slider.querySelector('.pc-slider-track');
        var slides = slider.querySelectorAll('.pc-slide');
        var dots   = slider.querySelectorAll('.pc-slider-dot');
        var n = slides.length;
        if (!track || n < 2) return;
        var idx = (parseInt(slider.dataset.index || '0', 10) + dir + n) % n;
        slider.dataset.index = idx;
        track.style.transform = 'translateX(' + (-idx * 100) + '%)';
        dots.forEach(function (d, i) { d.classList.toggle('active', i === idx); });
    };
})();
</script>
@endpush
@endonce
