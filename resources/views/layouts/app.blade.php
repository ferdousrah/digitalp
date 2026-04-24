<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Digital Support'))</title>
    <meta name="description" content="@yield('meta_description', 'Digital Support - Your Digital Products Partner')">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @php
        use App\Services\SettingService;
        $fontEnglish  = SettingService::get('font_english', 'Inter');
        $fontBangla   = SettingService::get('font_bangla', 'Hind Siliguri');
        $fontSizeBase = SettingService::get('font_size_base', '16');
        $googleFontsUrl = 'https://fonts.googleapis.com/css2?family='
            . urlencode($fontEnglish) . ':wght@300;400;500;600;700&family='
            . urlencode($fontBangla)  . ':wght@300;400;500;600;700&display=swap';
        $siteFavicon = SettingService::get('site_favicon');

        // Load all template color settings using defaults from TemplateSettings page
        $tc = \App\Filament\Pages\TemplateSettings::defaults();
        foreach ($tc as $k => $default) {
            $tc[$k] = SettingService::get($k, $default);
        }
    @endphp
    <link rel="stylesheet" href="{{ $googleFontsUrl }}">
    <style>
        :root {
            --font-english:          '{{ $fontEnglish }}', sans-serif;
            --font-bangla:           '{{ $fontBangla }}', sans-serif;
            --color-primary:         {{ $tc['color_primary'] }};
            --color-primary-text:    {{ $tc['color_primary_text'] }};
            --color-primary-hover:   {{ $tc['color_primary_hover'] }};
            --color-accent:          {{ $tc['color_accent'] }};
            --color-accent-text:     {{ $tc['color_accent_text'] }};
            --color-accent-hover:    {{ $tc['color_accent_hover'] }};
            font-size: {{ $fontSizeBase }}px;
        }

        /* ── Fonts ── */
        body, button, input, select, textarea { font-family: var(--font-english); }
        :lang(bn), .font-bangla, [data-lang="bn"] { font-family: var(--font-bangla); }

        /* ── Top bar ── */
        #top-bar      { background: {{ $tc['color_top_bar_bg'] }}   !important; color: {{ $tc['color_top_bar_text'] }}   !important; }
        #top-bar a    { color: {{ $tc['color_top_bar_text'] }} !important; opacity: 0.85; }
        #top-bar a:hover { color: {{ $tc['color_top_bar_text'] }} !important; opacity: 1; }

        /* ── Header main bar ── */
        #main-bar           { background: {{ $tc['color_header_bg'] }} !important; }
        #main-bar a,
        #main-bar button    { color: {{ $tc['color_header_text'] }} !important; }
        #main-bar a:hover,
        #main-bar button:hover { color: {{ $tc['color_header_icon_hover'] }} !important; }

        /* ── Search focus ring ── */
        [data-search-input]:focus { border-color: {{ $tc['color_primary'] }} !important; box-shadow: 0 0 0 3px {{ $tc['color_primary'] }}26 !important; }

        /* ── Nav / cat bar ── */
        #cat-bar                   { background: {{ $tc['color_nav_bg'] }} !important; }
        #cat-bar a                 { color: {{ $tc['color_nav_text'] }} !important; }
        #cat-bar a:hover,
        #cat-bar a.active          { background: {{ $tc['color_nav_hover_bg'] }} !important; color: {{ $tc['color_nav_hover_text'] }} !important; }

        /* ── Nav submenu / dropdown (must be AFTER #cat-bar rules to win on equal specificity) ── */
        #cat-bar .nav-submenu         { background: {{ $tc['color_nav_submenu_bg'] }} !important; border-top: 2px solid {{ $tc['color_nav_submenu_border'] }} !important; }
        #cat-bar .nav-submenu a       { background: transparent !important; color: {{ $tc['color_nav_submenu_text'] }} !important; }
        #cat-bar .nav-submenu a:hover { background: {{ $tc['color_nav_submenu_hover_bg'] }} !important; color: {{ $tc['color_nav_submenu_hover_text'] }} !important; }

        /* ── Product card hover ── */
        .product-card:hover         { border-color: {{ $tc['color_primary'] }} !important; }
        .product-card-atc button    { background: {{ $tc['color_btn_cart_bg'] }} !important; color: {{ $tc['color_btn_cart_text'] }} !important; }
        .product-card-atc button:hover { background: {{ $tc['color_btn_cart_hover_bg'] }} !important; color: {{ $tc['color_btn_cart_hover_text'] }} !important; }

        /* ── Detail page buttons ── */
        .btn-cart  { background: {{ $tc['color_btn_cart_bg'] }}  !important; color: {{ $tc['color_btn_cart_text'] }}  !important; }
        .btn-cart:hover  { background: {{ $tc['color_btn_cart_hover_bg'] }}  !important; color: {{ $tc['color_btn_cart_hover_text'] }}  !important; }
        .btn-buy   { background: {{ $tc['color_btn_buy_bg'] }}   !important; color: {{ $tc['color_btn_buy_text'] }}   !important; }
        .btn-buy:hover   { background: {{ $tc['color_btn_buy_hover_bg'] }}   !important; color: {{ $tc['color_btn_buy_hover_text'] }}   !important; }
        .btn-wa    { background: {{ $tc['color_btn_wa_bg'] }}    !important; color: {{ $tc['color_btn_wa_text'] }}    !important; }
        .btn-wa:hover    { background: {{ $tc['color_btn_wa_hover_bg'] }}    !important; color: {{ $tc['color_btn_wa_hover_text'] }}    !important; }
        .btn-call  { background: {{ $tc['color_btn_call_bg'] }}  !important; color: {{ $tc['color_btn_call_text'] }}  !important; }
        .btn-call:hover  { background: {{ $tc['color_btn_call_hover_bg'] }}  !important; color: {{ $tc['color_btn_call_hover_text'] }}  !important; }

        /* ── Footer ── */
        footer                                      { background: {{ $tc['color_footer_bg'] }}      !important; }
        footer, footer p, footer span               { color: {{ $tc['color_footer_text'] }}     !important; }
        footer h1,footer h2,footer h3,footer h4,
        footer h5,footer h6,.footer-heading         { color: {{ $tc['color_footer_heading'] }}  !important; }
        footer a                                    { color: {{ $tc['color_footer_link'] }}      !important; }
        footer a:hover                              { color: {{ $tc['color_footer_link_hover'] }}!important; }
        footer .hover\:bg-primary-600:hover         { background: {{ $tc['color_footer_link_hover'] }} !important; }

        /* ── Tailwind primary class overrides ── */
        .text-primary-400,.text-primary-500,.text-primary-600 { color: {{ $tc['color_primary'] }} !important; }
        .bg-primary-600    { background: {{ $tc['color_primary'] }} !important; }
        .border-primary-500,.border-primary-600 { border-color: {{ $tc['color_primary'] }} !important; }
        .hover\:text-primary-400:hover { color: {{ $tc['color_primary'] }} !important; }
        .hover\:bg-primary-600:hover   { background: {{ $tc['color_primary'] }} !important; }
    </style>
    <link rel="icon" href="{{ $siteFavicon ? Storage::disk('public')->url($siteFavicon) : asset('favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-rounded/css/uicons-solid-rounded.css'>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary-500 text-white px-4 py-2 rounded z-50">
        Skip to main content
    </a>

    @include('layouts.partials.header')

    <main id="main-content" class="flex-1">
        @yield('content')
    </main>

    @include('layouts.partials.footer')

    @include('layouts.partials.cart-sidebar')

    <!-- Floating buttons: Back to Top + WhatsApp -->
    @php $whatsapp = \App\Services\SettingService::get('contact_whatsapp', ''); @endphp
    <div style="position: fixed; bottom: 24px; right: 24px; z-index: 50; display: flex; flex-direction: column; align-items: center; gap: 12px;">
        <!-- Back to Top -->
        <button id="back-to-top" aria-label="Back to top" style="display: none; opacity: 0; width: 48px; height: 48px; border-radius: 50%; background: #16a34a; color: #fff; border: none; cursor: pointer; box-shadow: 0 4px 12px rgba(0,0,0,0.2); transition: opacity 0.3s ease, transform 0.3s ease; align-items: center; justify-content: center;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M18 15l-6-6-6 6"></path></svg>
        </button>

        <!-- WhatsApp -->
        @if($whatsapp)
        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $whatsapp) }}" target="_blank" rel="noopener" aria-label="Chat on WhatsApp" style="display: flex; width: 56px; height: 56px; border-radius: 50%; background: #25d366; color: #fff; align-items: center; justify-content: center; box-shadow: 0 4px 12px rgba(0,0,0,0.2); transition: transform 0.3s ease;" onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform='scale(1)'">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
        </a>
        @endif
    </div>

    <script>
    (function () {
        var btn = document.getElementById('back-to-top');
        if (!btn) return;
        var visible = false;

        window.addEventListener('scroll', function () {
            var show = window.scrollY > 300;
            if (show === visible) return;
            visible = show;
            if (visible) {
                btn.style.display = 'flex';
                setTimeout(function () { btn.style.opacity = '1'; btn.style.transform = 'scale(1)'; }, 10);
            } else {
                btn.style.opacity = '0';
                btn.style.transform = 'scale(0.8)';
                setTimeout(function () { if (!visible) btn.style.display = 'none'; }, 300);
            }
        }, { passive: true });

        btn.addEventListener('click', function () {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        btn.addEventListener('mouseover', function () { btn.style.transform = 'scale(1.1)'; });
        btn.addEventListener('mouseout', function () { btn.style.transform = 'scale(1)'; });
    })();
    </script>

    <!-- Quick View Modal -->
    <div id="quick-view-overlay" onclick="if(event.target===this)closeQuickView()" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center; padding:20px; opacity:0; transition:opacity 0.25s ease;">
        <div id="quick-view-modal" style="background:#fff; border-radius:16px; max-width:720px; width:100%; max-height:90vh; overflow-y:auto; padding:28px; position:relative; transform:scale(0.95) translateY(10px); transition:transform 0.25s ease; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <button onclick="closeQuickView()" style="position:absolute; top:12px; right:12px; width:32px; height:32px; border:none; background:#f3f4f6; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; z-index:10; transition:background 0.2s;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                <svg style="width:18px; height:18px; color:#6b7280;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
            <div id="quick-view-content">
                <div style="display:flex; align-items:center; justify-content:center; padding:60px 0;">
                    <svg style="width:32px;height:32px;animation:spin 1s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-opacity="0.2"/><path d="M12 2a10 10 0 019.8 8" stroke-linecap="round"/></svg>
                </div>
            </div>
        </div>
    </div>
    <script>
    function openQuickView(url) {
        var overlay = document.getElementById('quick-view-overlay');
        var modal = document.getElementById('quick-view-modal');
        var content = document.getElementById('quick-view-content');
        content.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;padding:60px 0;"><svg style="width:32px;height:32px;animation:spin 1s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-opacity="0.2"/><path d="M12 2a10 10 0 019.8 8" stroke-linecap="round"/></svg></div>';
        overlay.style.display = 'flex';
        setTimeout(function() { overlay.style.opacity = '1'; modal.style.transform = 'scale(1) translateY(0)'; }, 10);
        document.body.style.overflow = 'hidden';
        fetch(url)
            .then(function(res) { return res.text(); })
            .then(function(html) { content.innerHTML = html; })
            .catch(function() { content.innerHTML = '<p style="text-align:center;color:#ef4444;padding:40px;">Failed to load product details.</p>'; });
    }
    function closeQuickView() {
        var overlay = document.getElementById('quick-view-overlay');
        var modal = document.getElementById('quick-view-modal');
        overlay.style.opacity = '0';
        modal.style.transform = 'scale(0.95) translateY(10px)';
        setTimeout(function() { overlay.style.display = 'none'; document.body.style.overflow = ''; }, 250);
    }
    document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closeQuickView(); });
    </script>

    <!-- Toast Notification Container -->
    <div id="toast-container" style="position:fixed; top:24px; right:24px; z-index:200; display:flex; flex-direction:column; gap:10px; pointer-events:none;"></div>
    <script>
    function showToast(message, type) {
        type = type || 'success';
        var container = document.getElementById('toast-container');
        var toast = document.createElement('div');
        var colors = {
            success: { bg: '#f0fdf4', border: '#16a34a', text: '#15803d', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>' },
            error:   { bg: '#fef2f2', border: '#ef4444', text: '#dc2626', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>' },
            info:    { bg: '#eff6ff', border: '#3b82f6', text: '#2563eb', icon: '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20 10 10 0 000-20z"/>' }
        };
        var c = colors[type] || colors.success;
        toast.style.cssText = 'pointer-events:auto; display:flex; align-items:center; gap:10px; padding:14px 20px; background:' + c.bg + '; border:1px solid ' + c.border + '; border-left:4px solid ' + c.border + '; border-radius:10px; box-shadow:0 4px 20px rgba(0,0,0,0.1); color:' + c.text + '; font-size:0.9rem; font-weight:500; min-width:280px; max-width:420px; transform:translateX(120%); transition:transform 0.35s cubic-bezier(.4,0,.2,1), opacity 0.35s; opacity:0;';
        toast.innerHTML = '<svg style="width:20px;height:20px;flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24">' + c.icon + '</svg><span style="flex:1;">' + message + '</span><button onclick="this.parentElement.style.transform=\'translateX(120%)\';this.parentElement.style.opacity=\'0\';setTimeout(function(){toast.remove()},350)" style="background:none;border:none;cursor:pointer;padding:2px;color:' + c.text + ';opacity:0.6;"><svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>';
        container.appendChild(toast);
        setTimeout(function() { toast.style.transform = 'translateX(0)'; toast.style.opacity = '1'; }, 10);
        setTimeout(function() { toast.style.transform = 'translateX(120%)'; toast.style.opacity = '0'; setTimeout(function() { toast.remove(); }, 350); }, 3500);
    }

    // AJAX helpers for Compare & Wishlist
    function toggleWishlist(productId, btn) {
        var url = '{{ url("wishlist/toggle") }}/' + productId;
        fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            showToast(data.message, data.added ? 'success' : 'info');
            // Update header badge
            var badge = document.getElementById('wishlist-badge');
            if (badge) { badge.textContent = data.count; badge.style.display = data.count > 0 ? 'flex' : 'none'; }
            // Toggle heart fill state
            document.querySelectorAll('.wishlist-btn-' + productId).forEach(function(b) {
                var svg = b.querySelector('svg');
                if (svg) { svg.setAttribute('fill', data.added ? 'currentColor' : 'none'); }
                if (data.added) { b.classList.add('wishlisted'); } else { b.classList.remove('wishlisted'); }
            });
        })
        .catch(function() { showToast('Something went wrong.', 'error'); });
    }

    function toggleCompare(productId, btn) {
        var url = '{{ url("compare/add") }}/' + productId;
        fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.error) { showToast(data.message, 'error'); return; }
            showToast(data.message, data.added ? 'success' : 'info');
            // Update header badge
            var badge = document.getElementById('compare-badge');
            if (badge) { badge.textContent = data.count; badge.style.display = data.count > 0 ? 'flex' : 'none'; }
            // Toggle compare button state
            document.querySelectorAll('.compare-btn-' + productId).forEach(function(b) {
                if (data.added) { b.classList.add('compared'); } else { b.classList.remove('compared'); }
            });
        })
        .catch(function() { showToast('Something went wrong.', 'error'); });
    }
    </script>

    <script>
    (function () {
        var CART_ADD_URL    = '{{ url("cart/add") }}';
        var CART_UPDATE_URL = '{{ url("cart/update") }}';
        var CART_REMOVE_URL = '{{ url("cart/remove") }}';
        var CART_CLEAR_URL  = '{{ url("cart/clear") }}';
        var CART_DATA_URL   = '{{ route("cart.data") }}';
        var CART_SUGG_URL   = '{{ route("cart.suggestions") }}';
        var CSRF            = document.querySelector('meta[name="csrf-token"]').content;

        /* ── suggestions state ── */
        var sugg_products = [];
        var sugg_index = 0;       // per-card index
        var SUGG_CARD_W = 232;    // card px width (sidebar 380 - 32px padding = 348; ~67%, shows ~1.5 cards)
        var SUGG_STEP   = 242;    // card + 10px gap

        /* ── helpers ── */
        function apiPost(url, method, body) {
            return fetch(url, {
                method: method || 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: body ? JSON.stringify(body) : undefined,
            }).then(function (r) { return r.json(); });
        }

        /* ── sidebar open / close ── */
        window.cartOpen = function () {
            var overlay = document.getElementById('cart-overlay');
            var sidebar = document.getElementById('cart-sidebar');
            overlay.style.display = 'block';
            document.body.style.overflow = 'hidden';
            setTimeout(function () { overlay.style.opacity = '1'; sidebar.style.transform = 'translateX(0)'; }, 10);
        };
        window.cartClose = function () {
            var overlay = document.getElementById('cart-overlay');
            var sidebar = document.getElementById('cart-sidebar');
            overlay.style.opacity = '0';
            sidebar.style.transform = 'translateX(100%)';
            setTimeout(function () { overlay.style.display = 'none'; document.body.style.overflow = ''; }, 350);
        };

        /* ── render cart items ── */
        function renderCart(data) {
            var items   = data.items || [];
            var total   = data.total || '0.00';
            var count   = data.itemCount || 0;

            // floating button
            var floatLabel = document.getElementById('cart-float-label');
            var floatTotal = document.getElementById('cart-float-total');
            if (floatLabel) floatLabel.textContent = count + (count === 1 ? ' Item' : ' Items');
            if (floatTotal) floatTotal.textContent = total + '৳';

            // header badge
            var hdrBadge = document.getElementById('cart-header-badge');
            if (hdrBadge) { hdrBadge.textContent = count; hdrBadge.style.display = count > 0 ? 'flex' : 'none'; }

            var container = document.getElementById('cart-items');
            var footer    = document.getElementById('cart-footer');
            if (!container) return;

            var suggSection = document.getElementById('cart-suggestions');

            if (items.length === 0) {
                footer.style.display = 'none';
                if (suggSection) suggSection.style.display = 'none';
                container.innerHTML = '<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:60px 20px;gap:16px;">'
                    + '<svg style="width:80px;height:80px;color:#e5e7eb;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>'
                    + '<p style="font-size:0.875rem;color:#9ca3af;font-weight:500;margin:0;">No items in your cart!</p>'
                    + '</div>';
                return;
            }

            // Show suggestions when cart has items
            if (suggSection && sugg_products.length > 0) {
                suggSection.style.display = 'block';
                renderSuggestions();
            }

            footer.style.display = 'block';
            var sidebarTotal = document.getElementById('cart-sidebar-total');
            if (sidebarTotal) sidebarTotal.textContent = total + '৳';

            container.innerHTML = items.map(function (item) {
                var img = item.image
                    ? '<img src="' + item.image + '" alt="' + esc(item.name) + '" style="width:64px;height:64px;object-fit:cover;border-radius:8px;border:1px solid #f0f0f0;flex-shrink:0;">'
                    : '<span style="width:64px;height:64px;border-radius:8px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><svg style="width:28px;height:28px;color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>';

                var itemTotal = (parseFloat(item.price) * item.qty).toFixed(2);

                return '<div style="display:flex;gap:12px;padding:14px 20px;border-bottom:1px solid #f3f4f6;align-items:flex-start;">'
                    + img
                    + '<div style="flex:1;min-width:0;">'
                    +   '<div style="font-size:0.82rem;font-weight:600;color:#111827;line-height:1.4;margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">' + esc(item.name) + '</div>'
                    +   '<div style="font-size:0.8rem;color:#f97316;font-weight:700;margin-bottom:8px;">' + parseFloat(item.price).toFixed(2) + '৳</div>'
                    +   '<div style="display:flex;align-items:center;gap:8px;">'
                    +     '<div style="display:flex;align-items:center;border:1px solid #e5e7eb;border-radius:6px;overflow:hidden;">'
                    +       '<button onclick="cartUpdateQty(\'' + item.id + '\',' + (item.qty - 1) + ')" style="width:28px;height:28px;background:none;border:none;cursor:pointer;font-size:1rem;color:#374151;display:flex;align-items:center;justify-content:center;transition:background 0.15s;" onmouseover="this.style.background=\'#f3f4f6\'" onmouseout="this.style.background=\'none\'">−</button>'
                    +       '<span style="width:32px;text-align:center;font-size:0.8rem;font-weight:600;color:#111827;">' + item.qty + '</span>'
                    +       '<button onclick="cartUpdateQty(\'' + item.id + '\',' + (item.qty + 1) + ')" style="width:28px;height:28px;background:none;border:none;cursor:pointer;font-size:1rem;color:#374151;display:flex;align-items:center;justify-content:center;transition:background 0.15s;" onmouseover="this.style.background=\'#f3f4f6\'" onmouseout="this.style.background=\'none\'">+</button>'
                    +     '</div>'
                    +     '<span style="font-size:0.82rem;font-weight:600;color:#374151;margin-left:auto;">' + itemTotal + '৳</span>'
                    +   '</div>'
                    + '</div>'
                    + '<button onclick="cartRemove(\'' + item.id + '\')" style="background:none;border:none;cursor:pointer;padding:2px;color:#9ca3af;flex-shrink:0;transition:color 0.2s;" onmouseover="this.style.color=\'#ef4444\'" onmouseout="this.style.color=\'#9ca3af\'">'
                    +   '<svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>'
                    + '</button>'
                    + '</div>';
            }).join('');
        }

        /* ── public actions ── */
        window.addToCart = function (productId, btn) {
            var orig = btn ? btn.textContent : '';
            if (btn) { btn.textContent = '...'; btn.disabled = true; }
            apiPost(CART_ADD_URL + '/' + productId, 'POST')
                .then(function (data) {
                    renderCart(data);
                    shakeCartFloat();
                    showToast(data.message, 'success');
                })
                .catch(function () { showToast('Could not add to cart.', 'error'); })
                .finally(function () { if (btn) { btn.textContent = orig; btn.disabled = false; } });
        };

        window.orderNow = function (productId, btn) {
            if (btn) { btn.disabled = true; btn.style.opacity = '0.7'; }
            apiPost(CART_ADD_URL + '/' + productId, 'POST')
                .then(function (data) {
                    renderCart(data);
                    window.location.href = '{{ route("checkout.index") }}';
                })
                .catch(function () {
                    showToast('Could not process order.', 'error');
                    if (btn) { btn.disabled = false; btn.style.opacity = '1'; }
                });
        };

        function shakeCartFloat() {
            var el = document.getElementById('cart-float');
            if (!el) return;
            el.style.animation = 'none';
            // force reflow
            void el.offsetWidth;
            el.style.animation = 'cartShake 0.5s ease';
        }

        window.cartUpdateQty = function (key, qty) {
            apiPost(CART_UPDATE_URL + '/' + key, 'PATCH', { qty: qty })
                .then(function (data) { renderCart(data); });
        };

        window.cartRemove = function (key) {
            apiPost(CART_REMOVE_URL + '/' + key, 'DELETE')
                .then(function (data) { renderCart(data); });
        };

        window.cartClear = function () {
            apiPost(CART_CLEAR_URL, 'POST')
                .then(function (data) { renderCart(data); });
        };

        /* ── suggestions: load & render ── */
        function loadSuggestions() {
            fetch(CART_SUGG_URL, { headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    sugg_products = data.products || [];
                    sugg_index = 0;
                    // If cart already rendered with items, show suggestions now
                    var footer = document.getElementById('cart-footer');
                    var suggSection = document.getElementById('cart-suggestions');
                    if (suggSection && footer && footer.style.display !== 'none' && sugg_products.length > 0) {
                        suggSection.style.display = 'block';
                        renderSuggestions();
                    }
                })
                .catch(function () {});
        }

        function renderSuggestions() {
            var track = document.getElementById('sugg-track');
            if (!track || sugg_products.length === 0) return;

            // Render ALL cards; carousel slides via CSS transform
            track.innerHTML = sugg_products.map(function (p) {
                var img = p.image
                    ? '<img src="' + p.image + '" alt="' + esc(p.name) + '" style="width:62px;height:62px;object-fit:cover;border-radius:6px;flex-shrink:0;">'
                    : '<span style="width:62px;height:62px;border-radius:6px;background:#f3f4f6;display:flex;align-items:center;justify-content:center;flex-shrink:0;"><svg style="width:22px;height:22px;color:#d1d5db;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>';

                var priceHtml = '';
                if (p.compare_price) {
                    priceHtml += '<span style="font-size:0.67rem;color:#9ca3af;text-decoration:line-through;margin-right:4px;">' + p.compare_price + '৳</span>';
                }
                priceHtml += '<span style="font-size:0.75rem;color:#f97316;font-weight:700;">' + p.price + '৳</span>';

                return '<div style="min-width:' + SUGG_CARD_W + 'px;max-width:' + SUGG_CARD_W + 'px;background:#fff;border:1px solid #e5e7eb;border-radius:8px;padding:8px;display:flex;gap:8px;align-items:flex-start;box-shadow:0 1px 3px rgba(0,0,0,0.05);">'
                    + img
                    + '<div style="flex:1;min-width:0;">'
                    +   '<div style="font-size:0.72rem;font-weight:600;color:#111827;line-height:1.4;margin-bottom:4px;overflow:hidden;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;">' + esc(p.name) + '</div>'
                    +   '<div style="margin-bottom:5px;">' + priceHtml + '</div>'
                    +   '<button onclick="addToCart(' + p.id + ',this)" style="padding:3px 10px;background:#f97316;color:#fff;border:none;border-radius:4px;font-size:0.68rem;font-weight:700;cursor:pointer;transition:background 0.2s;" onmouseover="this.style.background=\'#ea6c0a\'" onmouseout="this.style.background=\'#f97316\'">+ Add</button>'
                    + '</div>'
                    + '</div>';
            }).join('');

            track.style.transform = 'translateX(0)';
            sugg_index = 0;
            updateSuggNav();
        }

        function updateSuggNav() {
            var prevBtn = document.getElementById('sugg-prev');
            var nextBtn = document.getElementById('sugg-next');
            if (prevBtn) prevBtn.style.opacity = sugg_index <= 0 ? '0.4' : '1';
            if (nextBtn) nextBtn.style.opacity = sugg_index >= sugg_products.length - 1 ? '0.4' : '1';
        }

        window.suggPrev = function () {
            if (sugg_index > 0) {
                sugg_index--;
                var track = document.getElementById('sugg-track');
                if (track) track.style.transform = 'translateX(-' + (sugg_index * SUGG_STEP) + 'px)';
                updateSuggNav();
            }
        };

        window.suggNext = function () {
            if (sugg_index < sugg_products.length - 1) {
                sugg_index++;
                var track = document.getElementById('sugg-track');
                if (track) track.style.transform = 'translateX(-' + (sugg_index * SUGG_STEP) + 'px)';
                updateSuggNav();
            }
        };

        /* ── init: load cart state on page load ── */
        fetch(CART_DATA_URL, { headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (data) { renderCart(data); })
            .catch(function () {});

        loadSuggestions();

        function esc(str) {
            return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }
    })();
    </script>

    @stack('scripts')

    <script>
    // Homepage section carousel helpers
    function hsCarouselNav(id, dir) {
        var el = document.getElementById(id);
        if (!el) return;
        var itemWidth = el.firstElementChild ? el.firstElementChild.offsetWidth + 20 : 200;
        el.scrollBy({ left: dir * itemWidth, behavior: 'smooth' });
    }
    // Mouse drag to scroll
    function hsCarouselDragStart(e, el) {
        el._drag = { active: true, x: e.pageX, left: el.scrollLeft };
        el.style.cursor = 'grabbing';
        el.style.userSelect = 'none';
    }
    function hsCarouselDragMove(e, el) {
        if (!el._drag || !el._drag.active) return;
        e.preventDefault();
        el.scrollLeft = el._drag.left - (e.pageX - el._drag.x);
    }
    function hsCarouselDragEnd(e, el) {
        if (!el._drag) return;
        el._drag.active = false;
        el.style.cursor = 'grab';
        el.style.userSelect = '';
    }
    // Touch swipe support
    function hsCarouselInit() {
        document.querySelectorAll('.hs-carousel').forEach(function(el) {
            el.style.cursor = 'grab';
            el.addEventListener('touchstart', function(e) {
                el._touch = { x: e.touches[0].clientX, left: el.scrollLeft };
            }, { passive: true });
            el.addEventListener('touchmove', function(e) {
                if (!el._touch) return;
                var dx = el._touch.x - e.touches[0].clientX;
                el.scrollLeft = el._touch.left + dx;
            }, { passive: true });
            el.addEventListener('touchend', function() {
                el._touch = null;
            }, { passive: true });
        });
    }
    document.addEventListener('DOMContentLoaded', hsCarouselInit);

    // Typewriter placeholder effect
    (function() {
        function typePlaceholder(el) {
            var full = el.getAttribute('data-ph') || el.getAttribute('placeholder') || '';
            if (!full) return;
            el.setAttribute('data-ph', full);
            el.setAttribute('placeholder', '');
            var i = 0;
            function type() {
                if (i <= full.length) {
                    el.setAttribute('placeholder', full.slice(0, i));
                    i++;
                    setTimeout(type, 45);
                } else {
                    // pause then erase then retype
                    setTimeout(function() {
                        erase();
                    }, 2800);
                }
            }
            function erase() {
                var cur = el.getAttribute('placeholder');
                if (cur.length > 0) {
                    el.setAttribute('placeholder', cur.slice(0, -1));
                    setTimeout(erase, 25);
                } else {
                    i = 0;
                    setTimeout(type, 500);
                }
            }
            // stagger start per element
            var idx = Array.from(document.querySelectorAll('input[placeholder], textarea[placeholder], input[data-ph], textarea[data-ph]')).indexOf(el);
            setTimeout(type, idx * 180);
        }

        document.querySelectorAll('input[placeholder]:not([type=hidden]):not([type=checkbox]):not([type=radio]), textarea[placeholder]').forEach(function(el) {
            // skip if already focused / has value
            el.addEventListener('focus', function() { el.setAttribute('placeholder', ''); });
            el.addEventListener('blur', function() {
                if (!el.value) {
                    el.setAttribute('placeholder', '');
                    el.setAttribute('data-ph', el.getAttribute('data-ph') || '');
                    // restart typing
                    setTimeout(function() { typePlaceholder(el); }, 300);
                }
            });
            typePlaceholder(el);
        });
    })();
    </script>
</body>
</html>
