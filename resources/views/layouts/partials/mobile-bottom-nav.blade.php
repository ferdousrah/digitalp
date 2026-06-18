{{-- ─────────────────────────────────────────────────────────────
     Mobile bottom navigation bar (lg:hidden)
     Persistent app-style bar pinned to the bottom on phones/tablets.
     Items: Home · Shop · Cart (opens drawer) · Wishlist · Account
     Badges sync with the same JS that drives the header badges:
       • cart    → #cart-bottom-badge   (updated in renderCart, app.blade.php)
       • wishlist → .wishlist-count-badge (updated in toggleWishlist, app.blade.php)
   ───────────────────────────────────────────────────────────── --}}
@php
    $mbActive   = '#16a34a';
    $mbIdle     = '#6b7280';
    $isHome     = request()->routeIs('home');
    $isShop     = request()->routeIs('products.*') || request()->routeIs('categories.*');
    $isWishlist = request()->routeIs('wishlist.*');
    $isAccount  = request()->routeIs('account.*') || request()->routeIs('login');
@endphp
<nav id="mobile-bottom-nav" aria-label="Primary mobile navigation"
     style="position:fixed; left:0; right:0; bottom:0; z-index:9985; background:#fff; border-top:1px solid #e5e7eb;
            box-shadow:0 -4px 16px rgba(0,0,0,0.06); padding-bottom:env(safe-area-inset-bottom);
            align-items:stretch; justify-content:space-around;">

    {{-- Home --}}
    <a href="{{ route('home') }}" aria-label="Home"
       style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:3px; padding:8px 0 7px; text-decoration:none; color:{{ $isHome ? $mbActive : $mbIdle }};">
        <i class="fi fi-{{ $isHome ? 'sr' : 'rr' }}-home" style="font-size:20px; line-height:1;"></i>
        <span style="font-size:0.66rem; font-weight:{{ $isHome ? '700' : '500' }};">{{ sc('navbar', 'home', 'Home') }}</span>
    </a>

    {{-- Shop --}}
    <a href="{{ route('products.index') }}" aria-label="Shop"
       style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:3px; padding:8px 0 7px; text-decoration:none; color:{{ $isShop ? $mbActive : $mbIdle }};">
        <i class="fi fi-{{ $isShop ? 'sr' : 'rr' }}-apps" style="font-size:20px; line-height:1;"></i>
        <span style="font-size:0.66rem; font-weight:{{ $isShop ? '700' : '500' }};">{{ sc('navbar', 'shop', 'Shop') }}</span>
    </a>

    {{-- Cart (opens drawer) --}}
    <button onclick="cartOpen()" aria-label="Cart"
            style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:3px; padding:8px 0 7px; background:none; border:none; cursor:pointer; color:{{ $mbIdle }}; position:relative;">
        <span style="position:relative; display:inline-flex; line-height:1;">
            <i class="fi fi-rr-shopping-cart" style="font-size:20px; line-height:1;"></i>
            <span id="cart-bottom-badge" style="position:absolute; top:-6px; right:-8px; background:#f97316; color:#fff; font-size:0.62rem; font-weight:700; min-width:16px; height:16px; padding:0 4px; border-radius:9px; display:none; align-items:center; justify-content:center; line-height:1;">0</span>
        </span>
        <span style="font-size:0.66rem; font-weight:500;">{{ sc('navbar', 'cart', 'Cart') }}</span>
    </button>

    {{-- Wishlist --}}
    <a href="{{ route('wishlist.index') }}" aria-label="Wishlist"
       style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:3px; padding:8px 0 7px; text-decoration:none; color:{{ $isWishlist ? '#ef4444' : $mbIdle }}; position:relative;">
        <span style="position:relative; display:inline-flex; line-height:1;">
            <i class="fi fi-{{ $isWishlist ? 'sr' : 'rr' }}-heart" style="font-size:20px; line-height:1;"></i>
            <span class="wishlist-count-badge" style="position:absolute; top:-6px; right:-8px; background:#ef4444; color:#fff; font-size:0.62rem; font-weight:700; min-width:16px; height:16px; padding:0 4px; border-radius:9px; display:{{ ($wishlistCount ?? 0) > 0 ? 'flex' : 'none' }}; align-items:center; justify-content:center; line-height:1;">{{ $wishlistCount ?? 0 }}</span>
        </span>
        <span style="font-size:0.66rem; font-weight:{{ $isWishlist ? '700' : '500' }};">{{ sc('navbar', 'wishlist', 'Wishlist') }}</span>
    </a>

    {{-- Account --}}
    <a href="{{ auth()->check() ? route('account.index') : route('login') }}" aria-label="Account"
       style="flex:1; display:flex; flex-direction:column; align-items:center; justify-content:center; gap:3px; padding:8px 0 7px; text-decoration:none; color:{{ $isAccount ? $mbActive : $mbIdle }};">
        <i class="fi fi-{{ $isAccount ? 'sr' : 'rr' }}-user" style="font-size:20px; line-height:1;"></i>
        <span style="font-size:0.66rem; font-weight:{{ $isAccount ? '700' : '500' }};">{{ auth()->check() ? sc('navbar', 'account', 'Account') : sc('navbar', 'sign_in', 'Sign In') }}</span>
    </a>
</nav>

{{-- Reserve space so the page footer and fixed widgets clear the bottom bar on mobile --}}
<style>
/* Hidden on desktop; only the mobile media query turns it on (inline display:flex
   would beat Tailwind's lg:hidden class, so visibility is controlled here instead). */
#mobile-bottom-nav { display: none; }
@media (max-width: 1023px) {
    #mobile-bottom-nav { display: flex; }
    body { padding-bottom: calc(58px + env(safe-area-inset-bottom)); }
    /* Lift the floating buttons (WhatsApp / back-to-top) and PWA banner above the bar */
    body > div[style*="bottom: 24px"] { bottom: calc(74px + env(safe-area-inset-bottom)) !important; }
    #pwa-install { bottom: calc(70px + env(safe-area-inset-bottom)) !important; }
    /* The right-edge floating cart is redundant on mobile now that the bottom bar has Cart */
    #cart-float { display: none !important; }
}
</style>
