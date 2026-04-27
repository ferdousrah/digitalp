<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Apply page-set @section('title') / @section('meta_description') to the central SeoService --}}
    @php
        $__pageTitle = trim((string) ($__env->yieldContent('title')));
        $__pageDesc  = trim((string) ($__env->yieldContent('meta_description')));
        $seoService  = app(\App\Services\SeoService::class);
        if ($__pageTitle !== '' && !$seoService->get('title'))       $seoService->title($__pageTitle);
        if ($__pageDesc  !== '' && !$seoService->get('description')) $seoService->description($__pageDesc);
    @endphp

    @include('partials.seo-head')
    @stack('seo')

    {{-- Preload the site logo — appears on every page in the header, so this earns its bytes everywhere --}}
    @php $__siteLogo = \App\Services\SettingService::get('site_logo'); @endphp
    @if($__siteLogo)
        <link rel="preload" as="image" href="{{ Storage::disk('public')->url($__siteLogo) }}" fetchpriority="high">
    @endif

    {{-- E-commerce tracking helper (window.dsTrack). `defer` ensures it doesn't block parsing
         and runs after HTML is fully parsed but before DOMContentLoaded. --}}
    <script defer src="{{ asset('js/tracking.js') }}?v={{ filemtime(public_path('js/tracking.js')) }}"></script>

    {{-- DNS prefetch + preconnect to cut RTT on first request to fonts CDN --}}
    <link rel="dns-prefetch" href="https://fonts.googleapis.com">
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link rel="dns-prefetch" href="https://cdn-uicons.flaticon.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdn-uicons.flaticon.com" crossorigin>
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

        /* ── Accessibility ── */
        /* Visible orange focus ring for keyboard users only (no mouse-click outline) */
        :focus { outline: none; }
        a:focus-visible,
        button:focus-visible,
        input:focus-visible,
        textarea:focus-visible,
        select:focus-visible,
        [role="button"]:focus-visible,
        [tabindex]:focus-visible {
            outline: 2px solid #f97316;
            outline-offset: 2px;
            border-radius: 4px;
        }
        /* Avoid the ring crashing into the orange Add-to-Cart's own background — invert when over orange */
        .bg-primary-500:focus-visible,
        .bg-primary-600:focus-visible,
        [style*="background:#f97316"]:focus-visible,
        [style*="background: #f97316"]:focus-visible {
            outline-color: #fff;
            box-shadow: 0 0 0 4px rgba(249,115,22,0.45);
        }

        /* Respect users who set "reduced motion" in OS settings — disable transitions and animations site-wide */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }

        /* ── View Transitions API — soft cross-fade between pages on supported browsers ── */
        @keyframes vt-fade-in  { from { opacity: 0; } to { opacity: 1; } }
        @keyframes vt-fade-out { from { opacity: 1; } to { opacity: 0; } }
        ::view-transition-old(root) {
            animation: vt-fade-out 0.18s cubic-bezier(.4, 0, .2, 1) both;
        }
        ::view-transition-new(root) {
            animation: vt-fade-in 0.28s cubic-bezier(.4, 0, .2, 1) both;
        }
        @media (prefers-reduced-motion: reduce) {
            ::view-transition-old(root), ::view-transition-new(root) { animation: none; }
        }

        /* ── Micro-interactions: tactile feedback on buttons and links ── */
        button:not(:disabled):active,
        a[href]:active {
            transform: translateY(1px);
        }
        @media (prefers-reduced-motion: reduce) {
            button:not(:disabled):active,
            a[href]:active { transform: none; }
        }
    </style>
    <link rel="icon" href="{{ $siteFavicon ? Storage::disk('public')->url($siteFavicon) : asset('favicon.ico') }}">

    {{-- Smooth cross-document navigation in supporting browsers (Chrome 111+, Edge, Opera).
         The hover-prefetch from Phase 2 already makes the next page load almost instant —
         this adds a soft cross-fade on top, so nav feels SPA-like without Barba intercepting. --}}
    <meta name="view-transition" content="same-origin">

    {{-- PWA — manifest + theme + iOS install meta --}}
    <link rel="manifest" href="{{ route('pwa.manifest') }}">
    <meta name="theme-color" content="{{ $tc['color_primary'] ?? '#16a34a' }}">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="{{ \App\Services\SettingService::get('site_short_name', \App\Services\SettingService::get('site_name', config('app.name'))) }}">
    @php $__pwaIcon = \App\Services\SettingService::get('site_logo'); @endphp
    @if($__pwaIcon)
        <link rel="apple-touch-icon" href="{{ Storage::disk('public')->url($__pwaIcon) }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Flaticon UICons — async-loaded so they don't block first paint.
         Pattern: load as `print` media (browsers download non-blockingly) then
         flip to `all` once the file arrives. <noscript> fallback for the rare
         no-JS visitor. --}}
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css" media="print" onload="this.media='all'; this.onload=null;">
    <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-rounded/css/uicons-solid-rounded.css" media="print" onload="this.media='all'; this.onload=null;">
    <noscript>
        <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css">
        <link rel="stylesheet" href="https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-rounded/css/uicons-solid-rounded.css">
    </noscript>
    @stack('styles')
</head>
<body class="min-h-screen flex flex-col">
    <a href="#main-content" class="sr-only focus:not-sr-only focus:absolute focus:top-4 focus:left-4 bg-primary-500 text-white px-4 py-2 rounded z-50">
        Skip to main content
    </a>

    @hasSection('hide_chrome')
    @else
        @include('layouts.partials.header')
    @endif

    <main id="main-content" class="flex-1">
        @yield('content')
    </main>

    @hasSection('hide_chrome')
    @else
        @include('layouts.partials.footer')
        @include('layouts.partials.cart-sidebar')
    @endif

    @hasSection('hide_chrome')
        {{-- chrome hidden on this page --}}
    @else
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
    @endif

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
            window.scrollTo({ top: 0, behavior: window.dsReducedMotion() ? 'auto' : 'smooth' });
        });

        btn.addEventListener('mouseover', function () { btn.style.transform = 'scale(1.1)'; });
        btn.addEventListener('mouseout', function () { btn.style.transform = 'scale(1)'; });
    })();

    // Single source of truth for the user's "reduced motion" preference.
    // Used by JS-driven effects (confetti, smooth scroll, cart shake, hero auto-advance)
    // since CSS-only rules can't catch JS-set inline styles or interval timers.
    if (typeof window.dsReducedMotion !== 'function') {
        window.dsReducedMotion = function () {
            return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        };
    }

    /* ── Hover prefetch ─────────────────────────────────────────────
       When a user hovers an internal link for ~65ms (a strong signal of
       intent to click), inject <link rel="prefetch"> so the next page is
       in cache by the time they actually click. Adds ~0 cost on desktop
       and is gated on slow/saving connections to respect data plans.
    */
    (function () {
        // Skip on slow connections or when user has data-saver on
        var conn = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        if (conn && (conn.saveData || /2g/.test(conn.effectiveType || ''))) return;

        var prefetched = new Set();
        var hoverTimer = null;

        function isPrefetchable(a) {
            if (!a || !a.href) return false;
            if (a.hasAttribute('download') || a.target === '_blank') return false;
            try {
                var u = new URL(a.href);
                if (u.origin !== window.location.origin) return false;
                // Skip noisy/dynamic endpoints — they're either authenticated, mutating, or auto-loaded
                if (/\/(admin|livewire|api|logout|cart\/(add|update|remove|clear|data)|auth\/|sslcommerz|bkash)/.test(u.pathname)) return false;
                // Skip same-page hash links
                if (u.pathname === window.location.pathname && u.hash) return false;
                if (prefetched.has(u.href)) return false;
                return true;
            } catch (e) { return false; }
        }

        function prefetch(url) {
            prefetched.add(url);
            var link = document.createElement('link');
            link.rel = 'prefetch';
            link.href = url;
            link.as = 'document';
            document.head.appendChild(link);
        }

        function start() {
            document.addEventListener('mouseover', function (e) {
                var a = e.target.closest && e.target.closest('a[href]');
                if (!isPrefetchable(a)) return;
                clearTimeout(hoverTimer);
                hoverTimer = setTimeout(function () { prefetch(a.href); }, 65);
            }, { passive: true });

            document.addEventListener('mouseout', function () { clearTimeout(hoverTimer); }, { passive: true });

            // Touch devices have no hover — start prefetch on touchstart instead (the user is committed)
            document.addEventListener('touchstart', function (e) {
                var a = e.target.closest && e.target.closest('a[href]');
                if (isPrefetchable(a)) prefetch(a.href);
            }, { passive: true });
        }

        // Defer until the browser is idle so prefetch never competes with first paint
        (window.requestIdleCallback || function (cb) { return setTimeout(cb, 1500); })(start);
    })();
    </script>

    <!-- Quick View Modal -->
    <div id="quick-view-overlay" onclick="if(event.target===this)closeQuickView()" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:100; align-items:center; justify-content:center; padding:20px; opacity:0; transition:opacity 0.25s ease;">
        <div id="quick-view-modal" role="dialog" aria-modal="true" aria-label="Product quick view" aria-hidden="true" style="background:#fff; border-radius:16px; max-width:720px; width:100%; max-height:90vh; overflow-y:auto; padding:28px; position:relative; transform:scale(0.95) translateY(10px); transition:transform 0.25s ease; box-shadow:0 20px 60px rgba(0,0,0,0.2);">
            <button onclick="closeQuickView()" aria-label="Close quick view" style="position:absolute; top:12px; right:12px; width:32px; height:32px; border:none; background:#f3f4f6; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; z-index:10; transition:background 0.2s;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
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
    var qvOpener = null;
    var qvKeyHandler = null;

    function _qvFocusable(root) {
        return Array.from(root.querySelectorAll(
            'a[href], button:not([disabled]), input:not([disabled]):not([type=hidden]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
        )).filter(function (el) { return el.offsetParent !== null; });
    }

    function openQuickView(url) {
        var overlay = document.getElementById('quick-view-overlay');
        var modal = document.getElementById('quick-view-modal');
        var content = document.getElementById('quick-view-content');
        qvOpener = document.activeElement;

        content.innerHTML = '<div style="display:flex;align-items:center;justify-content:center;padding:60px 0;"><svg style="width:32px;height:32px;animation:spin 1s linear infinite;" viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5"><circle cx="12" cy="12" r="10" stroke-opacity="0.2"/><path d="M12 2a10 10 0 019.8 8" stroke-linecap="round"/></svg></div>';
        overlay.style.display = 'flex';
        modal.setAttribute('aria-hidden', 'false');
        setTimeout(function() { overlay.style.opacity = '1'; modal.style.transform = 'scale(1) translateY(0)'; }, 10);
        var sbw = window.innerWidth - document.documentElement.clientWidth;
        if (sbw > 0) document.body.style.paddingRight = sbw + 'px';
        document.body.style.overflow = 'hidden';

        // Focus the close button so keyboard users can dismiss immediately or Tab into the content
        setTimeout(function () {
            var closeBtn = modal.querySelector('button[aria-label="Close quick view"]');
            if (closeBtn) closeBtn.focus();
        }, 50);

        // Tab focus trap + Esc to close
        qvKeyHandler = function (e) {
            if (e.key === 'Escape') { e.preventDefault(); closeQuickView(); return; }
            if (e.key !== 'Tab') return;
            var focusables = _qvFocusable(modal);
            if (focusables.length === 0) return;
            var first = focusables[0], last = focusables[focusables.length - 1];
            if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
            else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
        };
        document.addEventListener('keydown', qvKeyHandler);

        fetch(url)
            .then(function(res) { return res.text(); })
            .then(function(html) { content.innerHTML = html; })
            .catch(function() { content.innerHTML = '<p style="text-align:center;color:#ef4444;padding:40px;">Failed to load product details.</p>'; });
    }
    function closeQuickView() {
        var overlay = document.getElementById('quick-view-overlay');
        var modal = document.getElementById('quick-view-modal');
        if (!overlay || overlay.style.display === 'none') return;

        overlay.style.opacity = '0';
        modal.style.transform = 'scale(0.95) translateY(10px)';
        modal.setAttribute('aria-hidden', 'true');

        if (qvKeyHandler) { document.removeEventListener('keydown', qvKeyHandler); qvKeyHandler = null; }

        setTimeout(function() {
            overlay.style.display = 'none';
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
            if (qvOpener && typeof qvOpener.focus === 'function') {
                try { qvOpener.focus(); } catch (e) { /* element gone */ }
            }
            qvOpener = null;
        }, 250);
    }
    </script>

    <!-- Toast Notification Container -->
    <div id="toast-container" role="status" aria-live="polite" aria-atomic="true" style="position:fixed; top:24px; right:24px; z-index:200; display:flex; flex-direction:column; gap:10px; pointer-events:none;"></div>
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
        // Measure once to avoid jitter if invoked repeatedly mid-animation
        function getScrollbarWidth() {
            return window.innerWidth - document.documentElement.clientWidth;
        }

        // Track which element opened the drawer so we can return focus there on close.
        // Without focus restoration, keyboard users get "lost" in the page after closing.
        var cartOpener = null;
        var cartKeyHandler = null;

        function focusableIn(root) {
            return Array.from(root.querySelectorAll(
                'a[href], button:not([disabled]), input:not([disabled]):not([type=hidden]), select:not([disabled]), textarea:not([disabled]), [tabindex]:not([tabindex="-1"])'
            )).filter(function (el) { return el.offsetParent !== null; });
        }

        window.cartOpen = function () {
            var overlay = document.getElementById('cart-overlay');
            var sidebar = document.getElementById('cart-sidebar');
            cartOpener = document.activeElement;

            overlay.style.display = 'block';
            sidebar.setAttribute('aria-hidden', 'false');
            // Compensate for the disappearing scrollbar so layout stays steady
            var sbw = getScrollbarWidth();
            if (sbw > 0) document.body.style.paddingRight = sbw + 'px';
            document.body.style.overflow = 'hidden';

            setTimeout(function () { overlay.style.opacity = '1'; sidebar.style.transform = 'translateX(0)'; }, 10);

            // Move focus into the drawer so screen readers and keyboard users land inside the dialog
            setTimeout(function () {
                var first = focusableIn(sidebar)[0];
                if (first) first.focus();
            }, 50);

            // Esc to close + Tab focus trap
            cartKeyHandler = function (e) {
                if (e.key === 'Escape') { e.preventDefault(); window.cartClose(); return; }
                if (e.key !== 'Tab') return;
                var focusables = focusableIn(sidebar);
                if (focusables.length === 0) return;
                var first = focusables[0];
                var last  = focusables[focusables.length - 1];
                if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
                else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
            };
            document.addEventListener('keydown', cartKeyHandler);
        };
        window.cartClose = function () {
            var overlay = document.getElementById('cart-overlay');
            var sidebar = document.getElementById('cart-sidebar');
            overlay.style.opacity = '0';
            sidebar.style.transform = 'translateX(100%)';
            sidebar.setAttribute('aria-hidden', 'true');

            if (cartKeyHandler) { document.removeEventListener('keydown', cartKeyHandler); cartKeyHandler = null; }

            setTimeout(function () {
                overlay.style.display = 'none';
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                // Return focus to whatever opened the drawer (the cart icon, the float button, etc.)
                if (cartOpener && typeof cartOpener.focus === 'function') {
                    try { cartOpener.focus(); } catch (e) { /* element may have been removed */ }
                }
                cartOpener = null;
            }, 350);
        };

        /* ── render cart items ── */
        // Global money formatter — keep in sync with App\Support\Money::format()
        // Default: no decimals (matches the @@bdt Blade directive). Pass 2 for full precision invoices.
        if (typeof window.dsBdt !== 'function') {
            window.dsBdt = function (amount, decimals) {
                var num = parseFloat(amount);
                if (isNaN(num)) return '0৳';
                var d = (decimals == null) ? 0 : Number(decimals);
                return num.toLocaleString('en-US', {
                    minimumFractionDigits: d,
                    maximumFractionDigits: d,
                }) + '৳';
            };
        }
        function fmtBdt(n) {
            // Existing helper — keep returning the unit-less number (callsites add '৳' themselves)
            var num = parseFloat(n);
            if (isNaN(num)) return '0';
            return num.toLocaleString('en-US', { minimumFractionDigits: 0, maximumFractionDigits: 0 });
        }

        function renderCart(data) {
            var items   = data.items || [];
            var total   = data.total || 0;
            var count   = data.itemCount || 0;
            var totalFmt = window.dsBdt(total);

            // floating button
            var floatLabel = document.getElementById('cart-float-label');
            var floatTotal = document.getElementById('cart-float-total');
            if (floatLabel) floatLabel.textContent = count + (count === 1 ? ' Item' : ' Items');
            if (floatTotal) floatTotal.textContent = totalFmt;

            // Two distinct badges:
            //   - cart-header-badge   : small circular badge on the header cart icon (just a number)
            //   - cart-drawer-badge   : pill next to "Your Cart" inside the drawer ("X items")
            var hdrBadge = document.getElementById('cart-header-badge');
            if (hdrBadge) {
                hdrBadge.textContent = count;
                hdrBadge.style.display = count > 0 ? 'flex' : 'none';
            }
            var drawerBadge = document.getElementById('cart-drawer-badge');
            if (drawerBadge) {
                drawerBadge.textContent = count + (count === 1 ? ' item' : ' items');
                drawerBadge.style.display = count > 0 ? 'inline-block' : 'none';
            }

            var container = document.getElementById('cart-items');
            var footer    = document.getElementById('cart-footer');
            if (!container) return;

            var suggSection = document.getElementById('cart-suggestions');

            if (items.length === 0) {
                footer.style.display = 'none';
                if (suggSection) suggSection.style.display = 'none';
                container.innerHTML = '<div style="display:flex; flex-direction:column; align-items:center; justify-content:center; padding:60px 24px; gap:18px; height:100%;">'
                    + '<div style="width:88px; height:88px; border-radius:50%; background:#f8fafc; display:flex; align-items:center; justify-content:center;">'
                    +   '<svg style="width:42px; height:42px; color:#cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>'
                    + '</div>'
                    + '<div style="text-align:center;">'
                    +   '<p style="font-size:0.95rem; font-weight:700; color:#0f172a; margin:0 0 4px;">Your cart is empty</p>'
                    +   '<p style="font-size:0.8rem; color:#94a3b8; margin:0;">Browse our products to get started.</p>'
                    + '</div>'
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
            if (sidebarTotal) sidebarTotal.textContent = totalFmt;

            container.innerHTML = items.map(function (item) {
                var img = item.image
                    ? '<img src="' + item.image + '" alt="' + esc(item.name) + '" loading="lazy" decoding="async" width="72" height="72" style="width:72px; height:72px; object-fit:cover; border-radius:10px; background:#f8fafc; flex-shrink:0;">'
                    : '<span style="width:72px; height:72px; border-radius:10px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; flex-shrink:0;"><svg style="width:28px; height:28px; color:#cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>';

                var lineTotal = window.dsBdt(parseFloat(item.price) * item.qty);
                var unitPrice = window.dsBdt(item.price);

                return '<div class="cart-item-row" style="display:flex; gap:12px; padding:14px 20px; border-bottom:1px solid #f1f5f9; align-items:flex-start;">'
                    + img
                    + '<div style="flex:1; min-width:0; display:flex; flex-direction:column; gap:6px;">'
                    +   '<div style="display:flex; align-items:flex-start; gap:8px;">'
                    +     '<div style="font-size:0.82rem; font-weight:600; color:#0f172a; line-height:1.35; flex:1; min-width:0; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;">' + esc(item.name) + '</div>'
                    +     '<button onclick="cartRemove(\'' + item.id + '\')" aria-label="Remove" style="background:none; border:none; cursor:pointer; padding:0; color:#cbd5e1; flex-shrink:0; transition:color 0.15s; line-height:0;" onmouseover="this.style.color=\'#ef4444\'" onmouseout="this.style.color=\'#cbd5e1\'">'
                    +       '<svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>'
                    +     '</button>'
                    +   '</div>'
                    +   '<div style="font-size:0.72rem; color:#64748b;">' + unitPrice + ' each</div>'
                    +   '<div style="display:flex; align-items:center; justify-content:space-between; margin-top:2px;">'
                    +     '<div style="display:inline-flex; align-items:center; background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; overflow:hidden;">'
                    +       '<button onclick="cartUpdateQty(\'' + item.id + '\',' + (item.qty - 1) + ')" aria-label="Decrease" style="width:36px; height:36px; background:none; border:none; cursor:pointer; font-size:1rem; color:#475569; display:flex; align-items:center; justify-content:center; transition:background 0.15s;" onmouseover="this.style.background=\'#e2e8f0\'" onmouseout="this.style.background=\'transparent\'">−</button>'
                    +       '<span style="width:36px; text-align:center; font-size:0.82rem; font-weight:700; color:#0f172a;">' + item.qty + '</span>'
                    +       '<button onclick="cartUpdateQty(\'' + item.id + '\',' + (item.qty + 1) + ')" aria-label="Increase" style="width:36px; height:36px; background:none; border:none; cursor:pointer; font-size:1rem; color:#475569; display:flex; align-items:center; justify-content:center; transition:background 0.15s;" onmouseover="this.style.background=\'#e2e8f0\'" onmouseout="this.style.background=\'transparent\'">+</button>'
                    +     '</div>'
                    +     '<span style="font-size:0.92rem; font-weight:700; color:#0f172a; letter-spacing:-0.01em;">' + lineTotal + '</span>'
                    +   '</div>'
                    + '</div>'
                    + '</div>';
            }).join('');
        }

        /* ── public actions ── */
        // addToCart(productId)                — qty 1, no button feedback
        // addToCart(productId, btn)           — qty 1, button shows loading state
        // addToCart(productId, btn, qty)      — explicit qty (used on product detail page)
        // For Alpine bindings without a btn ref: addToCart(id, null, qty)
        window.addToCart = function (productId, btn, qty) {
            // Backwards-compat: if 2nd arg is a number, treat it as qty
            if (typeof btn === 'number') { qty = btn; btn = null; }
            qty = Math.max(1, parseInt(qty || 1, 10));

            var orig = btn ? btn.textContent : '';
            if (btn) { btn.textContent = '...'; btn.disabled = true; }
            apiPost(CART_ADD_URL + '/' + productId, 'POST', qty > 1 ? { qty: qty } : undefined)
                .then(function (data) {
                    renderCart(data);
                    shakeCartFloat();
                    showToast(data.message, 'success');
                    if (window.dsTrack && data.product) {
                        window.dsTrack('add_to_cart', {
                            id:    data.product.id ?? productId,
                            name:  data.product.name,
                            price: data.product.price,
                            qty:   data.product.qty ?? qty,
                        });
                    } else if (window.dsTrack) {
                        window.dsTrack('add_to_cart', { id: productId, qty: qty });
                    }
                })
                .catch(function () { showToast('Could not add to cart.', 'error'); })
                .finally(function () { if (btn) { btn.textContent = orig; btn.disabled = false; } });
        };

        window.orderNow = function (productId, btn, qty) {
            if (typeof btn === 'number') { qty = btn; btn = null; }
            qty = Math.max(1, parseInt(qty || 1, 10));
            if (btn) { btn.disabled = true; btn.style.opacity = '0.7'; }
            apiPost(CART_ADD_URL + '/' + productId, 'POST', qty > 1 ? { qty: qty } : undefined)
                .then(function (data) {
                    renderCart(data);
                    if (window.dsTrack && data.product) {
                        window.dsTrack('add_to_cart', {
                            id: data.product.id ?? productId,
                            name: data.product.name,
                            price: data.product.price,
                            qty: data.product.qty ?? qty,
                        });
                    }
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
            // Skip the playful shake for users who prefer reduced motion
            if (window.dsReducedMotion && window.dsReducedMotion()) return;
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
                    ? '<img src="' + p.image + '" alt="' + esc(p.name) + '" loading="lazy" decoding="async" width="62" height="62" style="width:62px; height:62px; object-fit:cover; border-radius:8px; flex-shrink:0; background:#f8fafc;">'
                    : '<span style="width:62px; height:62px; border-radius:8px; background:#f1f5f9; display:flex; align-items:center; justify-content:center; flex-shrink:0;"><svg style="width:22px; height:22px; color:#cbd5e1;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></span>';

                var priceHtml = '';
                if (p.compare_price) {
                    priceHtml += '<span style="font-size:0.66rem; color:#94a3b8; text-decoration:line-through; margin-right:5px;">' + window.dsBdt(p.compare_price) + '</span>';
                }
                priceHtml += '<span style="font-size:0.78rem; color:#f97316; font-weight:800;">' + window.dsBdt(p.price) + '</span>';

                return '<div style="min-width:' + SUGG_CARD_W + 'px; max-width:' + SUGG_CARD_W + 'px; background:#fff; border:1px solid #e2e8f0; border-radius:10px; padding:10px; display:flex; gap:10px; align-items:flex-start; box-shadow:0 1px 2px rgba(0,0,0,0.04); transition:all 0.2s;" onmouseover="this.style.borderColor=\'#fed7aa\';this.style.boxShadow=\'0 2px 8px rgba(249,115,22,0.08)\'" onmouseout="this.style.borderColor=\'#e2e8f0\';this.style.boxShadow=\'0 1px 2px rgba(0,0,0,0.04)\'">'
                    + img
                    + '<div style="flex:1; min-width:0;">'
                    +   '<div style="font-size:0.72rem; font-weight:600; color:#0f172a; line-height:1.4; margin-bottom:5px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">' + esc(p.name) + '</div>'
                    +   '<div style="margin-bottom:6px;">' + priceHtml + '</div>'
                    +   '<button onclick="addToCart(' + p.id + ',this)" style="display:inline-flex; align-items:center; gap:4px; padding:4px 10px; background:#fff7ed; color:#f97316; border:1px solid #fed7aa; border-radius:6px; font-size:0.7rem; font-weight:700; cursor:pointer; transition:all 0.15s;" onmouseover="this.style.background=\'#f97316\';this.style.color=\'#fff\';this.style.borderColor=\'#f97316\'" onmouseout="this.style.background=\'#fff7ed\';this.style.color=\'#f97316\';this.style.borderColor=\'#fed7aa\'">'
                    +     '<svg style="width:11px;height:11px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>Add'
                    +   '</button>'
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

    {{-- ─── PWA: install prompt banner ─── --}}
    <div id="pwa-install" role="dialog" aria-labelledby="pwa-install-title" aria-hidden="true" style="display:none; position:fixed; left:16px; right:16px; bottom:16px; z-index:9000; max-width:420px; margin:0 auto; background:#0f172a; color:#fff; border-radius:14px; padding:14px 16px; box-shadow:0 12px 32px rgba(15,23,42,0.3); border:1px solid rgba(255,255,255,0.06);">
        <div style="display:flex; align-items:center; gap:12px;">
            <div style="flex-shrink:0; width:40px; height:40px; border-radius:10px; background:rgba(249,115,22,0.15); border:1px solid rgba(249,115,22,0.4); display:flex; align-items:center; justify-content:center; color:#f97316;">
                <svg style="width:18px; height:18px;" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2z"/></svg>
            </div>
            <div style="flex:1; min-width:0;">
                <p id="pwa-install-title" style="margin:0; font-size:0.88rem; font-weight:700;">Install app</p>
                <p style="margin:2px 0 0; font-size:0.78rem; color:#94a3b8;">Faster access, works offline, no app store.</p>
            </div>
            <button id="pwa-install-btn" type="button" style="flex-shrink:0; padding:9px 16px; background:#f97316; color:#fff; font-size:0.78rem; font-weight:800; letter-spacing:0.06em; text-transform:uppercase; border:none; border-radius:8px; cursor:pointer; transition:background 0.2s;" onmouseover="this.style.background='#ea6c0a'" onmouseout="this.style.background='#f97316'">Install</button>
            <button id="pwa-install-dismiss" type="button" aria-label="Dismiss install prompt" style="flex-shrink:0; width:30px; height:30px; background:rgba(255,255,255,0.06); border:1px solid rgba(255,255,255,0.1); border-radius:8px; cursor:pointer; color:#cbd5e1; display:flex; align-items:center; justify-content:center; transition:all 0.2s;" onmouseover="this.style.background='rgba(255,255,255,0.12)'; this.style.color='#fff'" onmouseout="this.style.background='rgba(255,255,255,0.06)'; this.style.color='#cbd5e1'">
                <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
    </div>

    <script>
    /* ── PWA registration + install prompt ────────────────────────────
       Service worker caches HTML and static assets so subsequent visits
       are near-instant and a basic offline experience works. The install
       banner appears once `beforeinstallprompt` fires (Chrome/Edge/Android),
       and is hidden after install or 7-day dismissal cookie.
    */
    (function () {
        // Register the SW after page load so it never competes with first paint
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function () {
                navigator.serviceWorker.register('{{ asset('sw.js') }}?v={{ filemtime(public_path('sw.js')) }}', {
                    scope: '{{ url('/') }}/'
                }).catch(function () { /* SW failed — silent: user gets normal site */ });
            });
        }

        // ── Install prompt orchestration ──
        var deferredPrompt = null;
        var banner   = document.getElementById('pwa-install');
        var btnInstall = document.getElementById('pwa-install-btn');
        var btnDismiss = document.getElementById('pwa-install-dismiss');
        var DISMISS_KEY = 'ds_pwa_dismissed_until';
        var INSTALLED_KEY = 'ds_pwa_installed';

        function dismissedRecently() {
            try {
                var until = parseInt(localStorage.getItem(DISMISS_KEY) || '0', 10);
                return until > Date.now();
            } catch (e) { return false; }
        }

        function alreadyInstalled() {
            // Already running as PWA?
            if (window.matchMedia && window.matchMedia('(display-mode: standalone)').matches) return true;
            if (window.navigator.standalone) return true;
            try { return localStorage.getItem(INSTALLED_KEY) === '1'; } catch (e) { return false; }
        }

        function showBanner() {
            if (!banner) return;
            banner.style.display = 'block';
            banner.setAttribute('aria-hidden', 'false');
        }
        function hideBanner() {
            if (!banner) return;
            banner.style.display = 'none';
            banner.setAttribute('aria-hidden', 'true');
        }

        if (alreadyInstalled() || dismissedRecently()) hideBanner();

        window.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferredPrompt = e;
            if (alreadyInstalled() || dismissedRecently()) return;
            // Slight delay so it doesn't crash into the page on first paint
            setTimeout(showBanner, 2500);
        });

        if (btnInstall) btnInstall.addEventListener('click', function () {
            if (!deferredPrompt) { hideBanner(); return; }
            deferredPrompt.prompt();
            deferredPrompt.userChoice.then(function (choice) {
                if (choice.outcome === 'accepted') {
                    try { localStorage.setItem(INSTALLED_KEY, '1'); } catch (e) {}
                }
                deferredPrompt = null;
                hideBanner();
            });
        });

        if (btnDismiss) btnDismiss.addEventListener('click', function () {
            try { localStorage.setItem(DISMISS_KEY, String(Date.now() + 7 * 24 * 60 * 60 * 1000)); } catch (e) {}
            hideBanner();
        });

        window.addEventListener('appinstalled', function () {
            try { localStorage.setItem(INSTALLED_KEY, '1'); } catch (e) {}
            deferredPrompt = null;
            hideBanner();
        });
    })();
    </script>

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

    /* ── Auto-scroll for any element with data-autoscroll ──────────────────
       Continuously scrolls horizontally at the given speed (px per frame,
       defaults to 0.5). Pauses on hover, touch, drag, or keyboard focus
       inside the strip. Loops back to the start when reaching the end.

       When the strip's content already fits within its viewport (no overflow),
       we clone its children one or two times so there's something to scroll —
       otherwise auto-scroll silently does nothing on small lists. Cloned items
       are marked aria-hidden + tabindex=-1 so they don't pollute keyboard nav
       or screen reader output.

       Skipped entirely when prefers-reduced-motion is on.
    */
    document.addEventListener('DOMContentLoaded', function () {
        if (window.dsReducedMotion && window.dsReducedMotion()) return;

        function fillForScroll(el) {
            // Only clone when there's content but it doesn't overflow the strip
            if (!el.children.length) return;
            // Bail early if the strip is already scrollable
            if (el.scrollWidth - el.clientWidth > 1) return;

            var originals = Array.from(el.children);
            var safety = 0;
            while (el.scrollWidth - el.clientWidth < el.clientWidth && safety < 6) {
                originals.forEach(function (child) {
                    var clone = child.cloneNode(true);
                    clone.setAttribute('aria-hidden', 'true');
                    clone.dataset.autoscrollClone = '1';
                    clone.querySelectorAll('a, button, input, select, textarea').forEach(function (n) { n.tabIndex = -1; });
                    el.appendChild(clone);
                });
                safety++;
            }
        }

        document.querySelectorAll('[data-autoscroll]').forEach(function (el) {
            var speed = parseFloat(el.dataset.autoscroll) || 0.5;
            var paused = false;
            var visible = true;
            var raf;

            // scroll-snap fights smooth incremental scrolling — disable it on auto-scroll strips
            el.style.scrollSnapType = 'none';

            // Make sure there's enough content to scroll. Re-run on resize since
            // clientWidth changes with breakpoints.
            fillForScroll(el);
            window.addEventListener('resize', function () {
                // Strip clones first, then re-fill so cloning math stays correct
                Array.from(el.querySelectorAll('[data-autoscroll-clone]')).forEach(function (c) { c.remove(); });
                el.scrollLeft = 0;
                fillForScroll(el);
            }, { passive: true });

            function pause()  { paused = true;  }
            function resume() { paused = false; }

            // Pause when the user is interacting
            el.addEventListener('mouseenter', pause);
            el.addEventListener('mouseleave', resume);
            el.addEventListener('focusin',    pause);
            el.addEventListener('focusout',   resume);
            el.addEventListener('touchstart', pause, { passive: true });
            el.addEventListener('touchend',   resume, { passive: true });
            el.addEventListener('mousedown',  pause);
            document.addEventListener('mouseup', resume);

            // Pause when the strip is off-screen (saves CPU)
            if ('IntersectionObserver' in window) {
                var io = new IntersectionObserver(function (entries) {
                    visible = entries[0].isIntersecting;
                }, { threshold: 0 });
                io.observe(el);
            }

            function tick() {
                if (visible && !paused) {
                    var max = el.scrollWidth - el.clientWidth;
                    if (max > 1) {
                        el.scrollLeft += speed;
                        // Loop back to the start when reaching the end
                        if (el.scrollLeft >= max - 1) el.scrollLeft = 0;
                    }
                }
                raf = requestAnimationFrame(tick);
            }
            raf = requestAnimationFrame(tick);
        });
    });

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

        document.querySelectorAll('input[placeholder]:not([type=hidden]):not([type=checkbox]):not([type=radio]):not([data-no-type]), textarea[placeholder]:not([data-no-type])').forEach(function(el) {
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
