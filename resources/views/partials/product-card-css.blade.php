{{-- Product-card CSS — emitted once per page (see @once in product-card.blade.php).
     Static behaviour + per-device (Desktop/Tablet/Mobile) layout & visibility, all
     driven by ProductCardSettings. Colours stay global. --}}
@php
    use App\Filament\Pages\ProductCardSettings;
    use App\Services\SettingService;

    $pc = ProductCardSettings::defaults();
    foreach ($pc as $k => $d) { $pc[$k] = SettingService::get($k, $d); }

    // Mobile-first: base = mobile, then tablet (≥640), then desktop (≥1024)
    $deviceQueries = [
        'mobile'  => null,
        'tablet'  => '(min-width: 640px)',
        'desktop' => '(min-width: 1024px)',
    ];
    $on    = fn ($v) => in_array($v, ['1', 1, true, 'true'], true);
    $ratio = fn ($v) => $v ?: '1/1';
    $gaps  = ['mobile' => '12px', 'tablet' => '16px', 'desktop' => '24px'];
@endphp
<style>
    /* ── Static card behaviour (device-independent) ── */
    .product-card { transition: all 0.35s cubic-bezier(.4,0,.2,1); }
    .product-card:hover {
        box-shadow: 0 12px 40px {{ $pc['pc_card_hover_shadow'] }};
        border-color: {{ $pc['pc_card_hover_border'] }} !important;
    }
    .product-card:hover .product-card-actions,
    .product-card:focus-within .product-card-actions { transform: translateX(0) !important; opacity: 1 !important; }
    .product-card:hover .product-card-btns,
    .product-card:focus-within .product-card-btns { max-height: 60px !important; opacity: 1 !important; transform: translateY(0) !important; margin-top: 10px !important; }
    .product-card-actions > *:nth-child(1) { transition-delay: 0s; }
    .product-card-actions > *:nth-child(2) { transition-delay: 0.05s; }
    .product-card-actions > *:nth-child(3) { transition-delay: 0.1s; }
    .product-card-btns { transition-property: max-height, opacity, transform, margin-top; transition-timing-function: cubic-bezier(0.25,0.46,0.45,0.94); }
    .wishlisted { background:#ef4444 !important; } .wishlisted svg { color:#fff !important; }
    .compared  { background:#16a34a !important; } .compared svg  { color:#fff !important; }

    /* Touch devices have no hover — reveal actions + buttons by default */
    @media (hover: none), (pointer: coarse) {
        .product-card-actions { transform: translateX(0) !important; opacity: 1 !important; }
        .product-card-btns    { max-height: 60px !important; opacity: 1 !important; transform: translateY(0) !important; margin-top: 10px !important; }
    }

    /* ── Multi-thumbnail slider (prev/next reveal on hover; no autoplay) ── */
    .pc-slider-prev, .pc-slider-next {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 30px; height: 30px; border: none; border-radius: 50%;
        background: rgba(255,255,255,0.92); color: #111; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        font-size: 18px; line-height: 1; padding: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.18);
        opacity: 0; transition: opacity 0.25s ease, background 0.2s; z-index: 3;
    }
    .pc-slider-prev { left: 8px; }
    .pc-slider-next { right: 8px; }
    .pc-slider-prev:hover, .pc-slider-next:hover { background: #fff; }
    .product-card:hover .pc-slider-prev,
    .product-card:hover .pc-slider-next { opacity: 1; }
    .pc-slider-dots {
        position: absolute; bottom: 8px; left: 50%; transform: translateX(-50%);
        display: flex; gap: 5px; z-index: 3; pointer-events: none;
    }
    .pc-slider-dot { width: 6px; height: 6px; border-radius: 50%; background: rgba(255,255,255,0.55); box-shadow: 0 1px 2px rgba(0,0,0,0.25); transition: all 0.2s; }
    .pc-slider-dot.active { background: #fff; width: 16px; border-radius: 3px; }
    /* Touch devices: show arrows by default (no hover) */
    @media (hover: none), (pointer: coarse) {
        .pc-slider-prev, .pc-slider-next { opacity: 1; }
    }

    /* ── Per-device layout & visibility ── */
    @foreach($deviceQueries as $dev => $mq)
    @if($mq) @media {{ $mq }} { @endif
        .product-grid          { grid-template-columns: repeat({{ (int) ($pc["pc_columns_{$dev}"] ?? 2) }}, minmax(0, 1fr)) !important; gap: {{ $gaps[$dev] }} !important; }
        .product-card          { border-radius: {{ (int) $pc["pc_border_radius_{$dev}"] }}px; }
        .product-card-imglink  { aspect-ratio: {{ $ratio($pc["pc_image_ratio_{$dev}"]) }}; }
        .product-card:hover    { transform: translateY(-{{ (int) $pc["pc_hover_lift_{$dev}"] }}px); }
        .product-card:hover .product-card-img { transform: {{ $on($pc["pc_image_zoom_{$dev}"]) ? 'scale(1.08)' : 'none' }}; }
        .product-card-btns     { transition-duration: {{ (float) $pc["pc_btn_reveal_speed_{$dev}"] }}s; }
        .product-card-btns .pc-btn { border-radius: {{ (int) $pc["pc_btn_radius_{$dev}"] }}px; }

        @unless($on($pc["pc_show_brand_{$dev}"]))         .pc-brand         { display: none !important; } @endunless
        @unless($on($pc["pc_show_compare_price_{$dev}"])) .pc-compare-price { display: none !important; } @endunless
        @unless($on($pc["pc_show_sale_badge_{$dev}"]))    .pc-badge-sale    { display: none !important; } @endunless
        @unless($on($pc["pc_show_featured_badge_{$dev}"])).pc-badge-featured{ display: none !important; } @endunless
        @unless($on($pc["pc_show_wishlist_btn_{$dev}"]))  .pc-act-wishlist  { display: none !important; } @endunless
        @unless($on($pc["pc_show_compare_btn_{$dev}"]))   .pc-act-compare   { display: none !important; } @endunless
        @unless($on($pc["pc_show_quickview_btn_{$dev}"])) .pc-act-quickview { display: none !important; } @endunless
        @unless($on($pc["pc_show_cart_btn_{$dev}"]))      .pc-btn-cart      { display: none !important; } @endunless
        @unless($on($pc["pc_show_order_btn_{$dev}"]))     .pc-btn-order     { display: none !important; } @endunless

        {{-- Collapse whole clusters when nothing is left in them on this device --}}
        @if(! $on($pc["pc_show_wishlist_btn_{$dev}"]) && ! $on($pc["pc_show_compare_btn_{$dev}"]) && ! $on($pc["pc_show_quickview_btn_{$dev}"]))
        .product-card-actions { display: none !important; }
        @endif
        @if(! $on($pc["pc_show_cart_btn_{$dev}"]) && ! $on($pc["pc_show_order_btn_{$dev}"]))
        .product-card-btns { display: none !important; }
        @endif
    @if($mq) } @endif
    @endforeach
</style>
