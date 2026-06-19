{{-- ─────────────────────────────────────────────────────────────
     Mobile bottom navigation bar — fully admin-managed.
     Colors + items come from BottomNavSettings (Filament → Settings →
     Mobile Bottom Nav), stored via SettingService. Shown only ≤1023px.
     Cart badge syncs via #cart-bottom-badge (updated in renderCart, app.blade.php).
   ───────────────────────────────────────────────────────────── --}}
@php
    use App\Services\SettingService;
    use App\Filament\Pages\BottomNavSettings;

    $bn = [];
    foreach (BottomNavSettings::defaults() as $k => $d) {
        $bn[$k] = SettingService::get($k, $d);
    }
    $bnEnabled = in_array($bn['bottom_nav_enabled'] ?? '1', ['1', 1, true, 'true'], true);

    $bnItemsRaw = SettingService::get('bottom_nav_items');
    $bnItems    = $bnItemsRaw ? json_decode($bnItemsRaw, true) : null;
    if (! is_array($bnItems) || ! count($bnItems)) {
        $bnItems = BottomNavSettings::defaultItems();
    }

    $curPath = '/' . trim(request()->path(), '/');
    $curPath = $curPath === '/' ? '/' : rtrim($curPath, '/');
@endphp

@if($bnEnabled)
<nav id="mobile-bottom-nav" aria-label="Primary mobile navigation"
     style="--mbn-active:{{ $bn['bottom_nav_active_color'] }}; position:fixed; left:0; right:0; bottom:0; z-index:9985;
            background:{{ $bn['bottom_nav_bg'] }}; border-top:1px solid {{ $bn['bottom_nav_border'] }};
            box-shadow:0 -4px 16px rgba(0,0,0,0.06); padding-bottom:env(safe-area-inset-bottom);
            align-items:stretch; justify-content:space-around;">

    @foreach($bnItems as $item)
        @php
            $label  = $item['label']  ?? '';
            $icon   = trim($item['icon'] ?? 'fi-rr-link');
            $action = $item['action'] ?? 'link';
            $url    = $item['url']    ?? '/';

            $href     = null;
            $isActive = false;

            if ($action === 'cart') {
                // opens the cart drawer; no href
            } elseif ($action === 'account') {
                $href     = auth()->check() ? route('account.index') : route('login');
                $isActive = request()->routeIs('account.*') || request()->routeIs('login');
            } else { // link
                $rawPath  = parse_url($url, PHP_URL_PATH) ?: '/';
                $itemPath = '/' . trim($rawPath, '/');
                $itemPath = $itemPath === '/' ? '/' : rtrim($itemPath, '/');

                // Active when the current path matches (or is nested under) the item path
                if ($itemPath === '/') {
                    $isActive = ($curPath === '/');
                } else {
                    $isActive = ($curPath === $itemPath) || str_starts_with($curPath, $itemPath . '/');
                }

                // Respect a ?sale=1 style flag so "Offers" vs plain "Products" don't both light up
                parse_str(parse_url($url, PHP_URL_QUERY) ?: '', $itemQuery);
                if (array_key_exists('sale', $itemQuery)) {
                    $isActive = $isActive && request()->boolean('sale');
                } elseif ($itemPath === '/products') {
                    $isActive = $isActive && ! request()->boolean('sale');
                }

                // Build href (preserve query, base-path-aware for subdirectory installs)
                if (preg_match('#^https?://#i', $url) || str_starts_with($url, '#')) {
                    $href = $url;
                } else {
                    $parts = explode('?', $url, 2);
                    $href  = url($parts[0] ?: '/') . (isset($parts[1]) ? '?' . $parts[1] : '');
                }
            }

            $labelColor = $isActive ? $bn['bottom_nav_active_color'] : $bn['bottom_nav_text_color'];
            $iconColor  = $isActive ? $bn['bottom_nav_active_color'] : $bn['bottom_nav_icon_color'];
            $weight     = $isActive ? '700' : '500';
        @endphp

        @if($action === 'cart')
            <button onclick="cartOpen()" aria-label="{{ $label }}" class="mbn-item"
                    style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:3px; padding:8px 0 7px; background:none; border:none; cursor:pointer; color:{{ $labelColor }};">
                <span style="position:relative; display:inline-flex; line-height:1;">
                    <i class="fi {{ $icon }}" style="font-size:20px; line-height:1; color:{{ $iconColor }};"></i>
                    <span id="cart-bottom-badge" style="position:absolute; top:-6px; right:-8px; background:{{ $bn['bottom_nav_badge_bg'] }}; color:{{ $bn['bottom_nav_badge_text'] }}; font-size:0.62rem; font-weight:700; min-width:16px; height:16px; padding:0 4px; border-radius:9px; display:none; align-items:center; justify-content:center; line-height:1;">0</span>
                </span>
                <span style="font-size:0.66rem; font-weight:{{ $weight }};">{{ $label }}</span>
            </button>
        @else
            <a href="{{ $href }}" aria-label="{{ $label }}" class="mbn-item"
               style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:3px; padding:8px 0 7px; text-decoration:none; color:{{ $labelColor }};">
                <i class="fi {{ $icon }}" style="font-size:20px; line-height:1; color:{{ $iconColor }};"></i>
                <span style="font-size:0.66rem; font-weight:{{ $weight }};">{{ $label }}</span>
            </a>
        @endif
    @endforeach
</nav>

{{-- Reserve space so the page footer and fixed widgets clear the bar on mobile --}}
<style>
/* Hidden on desktop; only the mobile media query turns it on (inline display:flex
   would beat Tailwind's lg:hidden class, so visibility is controlled here instead). */
#mobile-bottom-nav { display: none; }
/* Hover/active tint (tablets / pointer devices) — !important to beat inline colors */
#mobile-bottom-nav .mbn-item:hover,
#mobile-bottom-nav .mbn-item:hover i { color: var(--mbn-active) !important; }
@media (max-width: 1023px) {
    #mobile-bottom-nav { display: flex; }
    body { padding-bottom: calc(58px + env(safe-area-inset-bottom)); }
    /* Lift the floating buttons (WhatsApp / back-to-top) and PWA banner above the bar */
    body > div[style*="bottom: 24px"] { bottom: calc(74px + env(safe-area-inset-bottom)) !important; }
    #pwa-install { bottom: calc(70px + env(safe-area-inset-bottom)) !important; }
}
</style>
@endif
