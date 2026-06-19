<div x-data="{ mobileMenuOpen: false, megaOpen: false, megaTimer: null, mobileProductsOpen: false }"
     x-effect="document.documentElement.style.overflow = mobileMenuOpen ? 'hidden' : ''; document.body.style.overflow = mobileMenuOpen ? 'hidden' : ''"
     @keydown.escape.window="if (mobileMenuOpen) { mobileMenuOpen = false; } if (megaOpen) { megaOpen = false; }">
<header id="site-header" class="bg-white sticky top-0 z-40" style="transition: top 0.3s ease; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">

    <!-- Announcement bar -->
    @if(\App\Services\SettingService::get('top_bar_enabled', '1') == '1')
    <div id="top-bar" style="background:#16a34a; color:#fff; font-size:0.8125rem;">
        <div class="container-custom flex justify-between items-center px-4 sm:px-6 lg:px-8" style="height:34px;">
            <span class="hidden sm:inline" style="opacity:0.9;">{{ sc('navbar', 'welcome', 'Welcome to ' . \App\Services\SettingService::get('site_name', config('app.name'))) }}</span>
            <div class="flex items-center gap-5 ml-auto">
                <a href="{{ route('contact.index') }}" style="color:rgba(255,255,255,0.85); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.85)'">{{ sc('navbar', 'contact', 'Contact') }}</a>
                <a href="{{ route('faq.index') }}" style="color:rgba(255,255,255,0.85); text-decoration:none; transition:color 0.2s;" onmouseover="this.style.color='#fff'" onmouseout="this.style.color='rgba(255,255,255,0.85)'">{{ sc('navbar', 'faq', 'FAQ') }}</a>

                <!-- Language Switcher -->
                @php $currentLocale = current_locale(); @endphp
                <div style="display:flex; align-items:center; gap:2px; border-left:1px solid rgba(255,255,255,0.3); padding-left:12px;">
                    <a href="{{ route('language.switch', 'en') }}"
                       style="display:inline-flex; align-items:center; gap:3px; padding:2px 7px; border-radius:4px; font-size:0.75rem; font-weight:600; text-decoration:none; transition:all 0.15s;
                              background:{{ $currentLocale === 'en' ? 'rgba(255,255,255,0.25)' : 'transparent' }};
                              color:{{ $currentLocale === 'en' ? '#fff' : 'rgba(255,255,255,0.7)' }};"
                       title="English">
                        🇬🇧 EN
                    </a>
                    <span style="color:rgba(255,255,255,0.4); font-size:0.7rem;">|</span>
                    <a href="{{ route('language.switch', 'bn') }}"
                       style="display:inline-flex; align-items:center; gap:3px; padding:2px 7px; border-radius:4px; font-size:0.75rem; font-weight:600; text-decoration:none; transition:all 0.15s;
                              background:{{ $currentLocale === 'bn' ? 'rgba(255,255,255,0.25)' : 'transparent' }};
                              color:{{ $currentLocale === 'bn' ? '#fff' : 'rgba(255,255,255,0.7)' }};"
                       title="বাংলা">
                        🇧🇩 বাং
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main bar: Logo + Search + Icons -->
    <div id="main-bar" style="background:#fff; border-bottom:1px solid #f0f0f0;">
        <div class="container-custom main-bar-inner flex items-center gap-4 px-4 sm:px-6 lg:px-8" style="height:92px; padding-top:10px; padding-bottom:10px; transition:height 0.3s ease;">

            <!-- Mobile hamburger (left — mobile only) -->
            <button @click="mobileMenuOpen = !mobileMenuOpen" class="mobile-hamburger" aria-label="Menu"
                    style="align-items:center; justify-content:center; width:42px; height:42px; padding:0; color:#374151; background:none; border:none; cursor:pointer;">
                <i x-show="!mobileMenuOpen" class="fi fi-rr-menu-burger" style="font-size:24px; line-height:1;"></i>
                <i x-show="mobileMenuOpen" class="fi fi-rr-cross" style="font-size:24px; line-height:1;"></i>
            </button>

            <!-- Logo -->
            @php
                $siteLogo = \App\Services\SettingService::get('site_logo');
                $siteName = \App\Services\SettingService::get('site_name', config('app.name'));
                // Split site_name on first space — first word in primary, rest in accent.
                // For one-word names ("Qloraa"), only the primary span renders.
                $nameParts = preg_split('/\s+/', trim($siteName), 2);
            @endphp
            <a href="{{ route('home') }}" class="header-logo-link" aria-label="{{ $siteName }}" style="flex-shrink:0; display:flex; align-items:center; text-decoration:none;">
                @if($siteLogo)
                    <img id="header-logo" src="{{ Storage::disk('public')->url($siteLogo) }}" alt="{{ $siteName }}" decoding="async" fetchpriority="high" style="height:52px; width:auto; transition:height 0.3s ease;">
                @else
                    <div id="header-logo" style="display:flex; flex-direction:column; line-height:1.1; transition:all 0.3s ease;">
                        <span style="font-size:1.2rem; font-weight:800; color:#16a34a; letter-spacing:-0.01em;">{{ $nameParts[0] }}</span>
                        @if(!empty($nameParts[1]))
                            <span style="font-size:1.2rem; font-weight:800; color:#ef4444; letter-spacing:-0.01em;">{{ $nameParts[1] }}</span>
                        @endif
                    </div>
                @endif
            </a>

            <!-- Search bar -->
            <div class="header-search" style="flex:1; max-width:640px; margin:0 auto;">
                <form action="{{ route('search') }}" method="GET" style="position:relative;">
                    <input type="text" name="q" data-search-input
                        data-autocomplete-url="{{ route('search.autocomplete') }}"
                        placeholder="{{ sc('navbar', 'search_placeholder', 'Search products...') }}"
                        style="width:100%; padding:11px 52px 11px 22px; border:1.5px solid #e5e7eb; border-radius:50px; font-size:0.9375rem; background:#f9fafb; outline:none; transition:border-color 0.2s, box-shadow 0.2s; box-sizing:border-box;"
                        onfocus="this.style.borderColor='#16a34a'; this.style.boxShadow='0 0 0 3px rgba(22,163,74,0.12)';"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none';"
                        autocomplete="off">
                    <button type="submit" aria-label="Search" style="position:absolute; right:0; top:0; bottom:0; width:52px; display:flex; align-items:center; justify-content:center; background:transparent; border:none; cursor:pointer; color:#9ca3af; transition:color 0.2s;" onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='#9ca3af'">
                        <i class="fi fi-rr-search" style="font-size:18px; line-height:1;"></i>
                    </button>
                    <div data-search-results class="hidden" style="position:absolute; top:calc(100% + 6px); left:0; right:0; background:#fff; border:1px solid #e5e7eb; border-radius:16px; box-shadow:0 8px 32px rgba(0,0,0,0.12); z-index:100; max-height:280px; overflow-y:auto;"></div>
                </form>
            </div>

            <!-- Right action icons -->
            <div class="header-actions" style="display:flex; align-items:center; flex-shrink:0;">

                <!-- Track Order (desktop only) -->
                <a href="{{ route('track-order.index') }}" style="display:none; flex-direction:column; align-items:center; gap:2px; padding:8px 12px; color:#374151; text-decoration:none; transition:color 0.2s;" class="lg-flex-col" onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='#374151'">
                    <i class="fi fi-rr-box-open" style="font-size:22px; line-height:1;"></i>
                    <span style="font-size:0.7125rem; font-weight:500; white-space:nowrap;">{{ sc('navbar', 'track_order', 'Track Order') }}</span>
                </a>

                <!-- Sign In / Account (desktop only) -->
                @auth
                    <div x-data="{ open:false }" @click.outside="open=false" class="lg-flex-col" style="display:none; position:relative;">
                        <button @click="open=!open" style="display:flex; flex-direction:column; align-items:center; gap:2px; padding:8px 12px; color:#374151; background:none; border:none; cursor:pointer; transition:color 0.2s;" onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='#374151'" aria-label="Account">
                            <i class="fi fi-rr-user" style="font-size:22px; line-height:1;"></i>
                            <span style="font-size:0.7125rem; font-weight:500; white-space:nowrap; max-width:90px; overflow:hidden; text-overflow:ellipsis;">{{ explode(' ', auth()->user()->name ?? 'Account')[0] ?: 'Account' }}</span>
                        </button>
                        <div x-show="open" x-cloak x-transition style="position:absolute; top:100%; right:0; min-width:200px; background:#fff; border:1px solid #e5e7eb; border-radius:10px; box-shadow:0 12px 28px rgba(15,23,42,0.12); padding:6px; z-index:60;">
                            <a href="{{ route('account.index') }}" style="display:flex; align-items:center; gap:10px; padding:10px 12px; color:#0f172a; text-decoration:none; font-size:0.88rem; font-weight:600; border-radius:6px;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <svg style="width:16px;height:16px; color:#64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                My Account
                            </a>
                            <a href="{{ route('account.orders') }}" style="display:flex; align-items:center; gap:10px; padding:10px 12px; color:#0f172a; text-decoration:none; font-size:0.88rem; font-weight:600; border-radius:6px;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                <svg style="width:16px;height:16px; color:#64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                My Orders
                            </a>
                            @if(!auth()->user()->isCustomer())
                                <a href="{{ url('/admin') }}" style="display:flex; align-items:center; gap:10px; padding:10px 12px; color:#0f172a; text-decoration:none; font-size:0.88rem; font-weight:600; border-radius:6px; border-top:1px solid #f1f5f9; margin-top:4px;" onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='transparent'">
                                    <svg style="width:16px;height:16px; color:#64748b;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                    Admin Panel
                                </a>
                            @endif
                            <form method="POST" action="{{ route('logout') }}" style="margin:0; border-top:1px solid #f1f5f9; padding-top:4px; margin-top:4px;">
                                @csrf
                                <button type="submit" style="display:flex; align-items:center; gap:10px; padding:10px 12px; color:#ef4444; background:none; border:none; cursor:pointer; font-size:0.88rem; font-weight:600; width:100%; text-align:left; border-radius:6px;" onmouseover="this.style.background='#fef2f2'" onmouseout="this.style.background='transparent'">
                                    <svg style="width:16px;height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Log out
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" style="display:none; flex-direction:column; align-items:center; gap:2px; padding:8px 12px; color:#374151; text-decoration:none; transition:color 0.2s;" class="lg-flex-col" onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='#374151'">
                        <i class="fi fi-rr-user" style="font-size:22px; line-height:1;"></i>
                        <span style="font-size:0.7125rem; font-weight:500; white-space:nowrap;">{{ sc('navbar', 'sign_in', 'Sign In') }}</span>
                    </a>
                @endauth

                <!-- Wishlist -->
                <a href="{{ route('wishlist.index') }}" class="header-action-wishlist" style="display:flex; flex-direction:column; align-items:center; gap:2px; padding:8px 10px; color:#374151; text-decoration:none; transition:color 0.2s; position:relative;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#374151'" aria-label="Wishlist">
                    <i class="fi fi-rr-heart" style="font-size:22px; line-height:1;"></i>
                    <span class="header-icon-label" style="font-size:0.7125rem; font-weight:500;">{{ sc('navbar', 'wishlist', 'Wishlist') }}</span>
                    <span id="wishlist-badge" style="position:absolute; top:2px; right:4px; background:#ef4444; color:#fff; font-size:0.6625rem; font-weight:700; min-width:16px; height:16px; border-radius:50%; display:{{ ($wishlistCount ?? 0) > 0 ? 'flex' : 'none' }}; align-items:center; justify-content:center; line-height:1;">{{ $wishlistCount ?? 0 }}</span>
                </a>

                <!-- Compare -->
                <a href="{{ route('compare.index') }}" class="header-action-compare" style="display:flex; flex-direction:column; align-items:center; gap:2px; padding:8px 10px; color:#374151; text-decoration:none; transition:color 0.2s; position:relative;" onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='#374151'" aria-label="Compare">
                    <i class="fi fi-rr-chart-histogram" style="font-size:22px; line-height:1;"></i>
                    <span class="header-icon-label" style="font-size:0.7125rem; font-weight:500;">{{ sc('navbar', 'compare', 'Compare') }}</span>
                    <span id="compare-badge" style="position:absolute; top:2px; right:4px; background:#f97316; color:#fff; font-size:0.6625rem; font-weight:700; min-width:16px; height:16px; border-radius:50%; display:{{ ($compareCount ?? 0) > 0 ? 'flex' : 'none' }}; align-items:center; justify-content:center; line-height:1;">{{ $compareCount ?? 0 }}</span>
                </a>

                <!-- Cart -->
                <button onclick="cartOpen()" style="display:flex; flex-direction:column; align-items:center; gap:2px; padding:8px 10px; color:#374151; background:none; border:none; cursor:pointer; transition:color 0.2s; position:relative;" onmouseover="this.style.color='#f97316'" onmouseout="this.style.color='#374151'" aria-label="Cart">
                    <i class="fi fi-rr-shopping-cart" style="font-size:22px; line-height:1;"></i>
                    <span class="header-icon-label" style="font-size:0.7125rem; font-weight:500;">{{ sc('navbar', 'cart', 'Cart') }}</span>
                    <span id="cart-header-badge" style="position:absolute; top:2px; right:4px; background:#f97316; color:#fff; font-size:0.6625rem; font-weight:700; min-width:16px; height:16px; border-radius:50%; display:none; align-items:center; justify-content:center; line-height:1;">0</span>
                </button>

            </div>
        </div>
    </div>


</header>

<!-- Mobile Sidebar Drawer -->
<div x-show="mobileMenuOpen" class="lg:hidden" style="position:fixed; inset:0; z-index:9999;">
    <!-- Backdrop -->
    <div x-show="mobileMenuOpen"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        @click="mobileMenuOpen = false"
        style="position:absolute; inset:0; background:rgba(0,0,0,0.5);"></div>

    <!-- Sidebar panel -->
    <div x-show="mobileMenuOpen"
        role="dialog" aria-modal="true" aria-label="Mobile navigation menu"
        :aria-hidden="!mobileMenuOpen"
        x-transition:enter="transition ease-out duration-300" x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="translate-x-0" x-transition:leave-end="-translate-x-full"
        style="position:absolute; top:0; left:0; bottom:0; width:300px; max-width:80vw; background:#fff; box-shadow:4px 0 24px rgba(0,0,0,0.15); display:flex; flex-direction:column;">

        <!-- Close button — sits just outside the panel's right edge, on the overlay (like the sample) -->
        <button @click="mobileMenuOpen = false" aria-label="Close menu"
            style="position:absolute; top:14px; right:-50px; width:44px; height:44px; display:flex; align-items:center; justify-content:center; border:none; background:transparent; cursor:pointer; color:#fff;">
            <svg style="width:34px; height:34px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        <!-- Sidebar Body (Shajgoj-style: user card · categories · quick links) -->
        <div class="mm-scroll" style="position:absolute; inset:0; overflow-y:auto; overscroll-behavior:contain; -webkit-overflow-scrolling:touch; padding:0 0 24px;">
            @php $mmUser = auth()->user(); @endphp

            <!-- Orange user card -->
            <a href="{{ $mmUser ? route('account.index') : route('login') }}"
               style="display:flex; align-items:center; gap:13px; margin:14px; padding:15px 16px; border-radius:14px; background:#f97316; text-decoration:none; box-shadow:0 6px 16px rgba(249,115,22,0.28);">
                <span style="width:46px; height:46px; border-radius:50%; background:#fff; display:flex; align-items:center; justify-content:center; flex-shrink:0; box-shadow:0 1px 3px rgba(0,0,0,0.12);">
                    <i class="fi fi-sr-user" style="font-size:24px; color:#94a3b8; line-height:1;"></i>
                </span>
                <span style="display:flex; flex-direction:column; line-height:1.25; min-width:0;">
                    <strong style="color:#fff; font-size:1.02rem; font-weight:800;">{{ $mmUser ? 'Hi, ' . explode(' ', $mmUser->name)[0] : 'Hello there!' }}</strong>
                    <span style="color:rgba(255,255,255,0.92); font-size:0.85rem;">{{ $mmUser ? 'My Account' : 'Sign in' }}</span>
                </span>
            </a>

            <!-- Category list -->
            <div style="margin:0 14px; background:#f5f5f5; border-radius:12px; overflow:hidden;">
                @foreach($megaCategories as $mmCat)
                    @if($mmCat->children->count())
                        <div x-data="{ open: false }" class="mm-row">
                            <button @click="open = !open" :aria-expanded="open" style="width:100%; display:flex; align-items:center; justify-content:space-between; gap:8px; padding:13px 16px; background:none; border:none; cursor:pointer; text-align:left;">
                                <span :style="{ color: open ? '#f97316' : '#333' }" style="font-size:0.74rem; font-weight:500;">{{ $mmCat->name }}</span>
                                <svg :style="{ transform: open ? 'rotate(-90deg)' : 'rotate(0deg)', color: open ? '#f97316' : '#c4c4c4' }" style="width:15px; height:15px; transition:transform 0.25s; flex-shrink:0;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <div x-show="open" x-collapse style="background:#fff;">
                                @foreach($mmCat->children as $mmSub)
                                    <a href="{{ route('categories.show', $mmSub) }}" class="mm-sub" style="display:block; padding:9px 16px 9px 32px; font-size:0.69rem; color:#555; text-decoration:none;">{{ $mmSub->name }}</a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a href="{{ route('categories.show', $mmCat) }}" class="mm-row" style="display:flex; align-items:center; padding:13px 16px; text-decoration:none;">
                            <span style="font-size:0.74rem; color:#333; font-weight:500;">{{ $mmCat->name }}</span>
                        </a>
                    @endif
                @endforeach
            </div>

            <!-- Quick Links -->
            <div style="padding:22px 18px 10px;">
                <span style="font-size:0.98rem; font-weight:800; color:#444;">Quick Links</span>
                <div style="width:34px; height:3px; background:#f97316; border-radius:2px; margin-top:6px;"></div>
            </div>
            @php $mmLocale = current_locale(); @endphp
            <div style="margin:0 14px; background:#f5f5f5; border-radius:12px; overflow:hidden;">
                <a href="{{ route('wishlist.index') }}" class="mm-row" style="display:flex; align-items:center; gap:13px; padding:13px 16px; text-decoration:none; color:#333;">
                    <svg style="width:20px; height:20px; color:#334155;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                    <span style="font-size:0.74rem; font-weight:500;">Wishlist</span>
                </a>
                <a href="{{ route('compare.index') }}" class="mm-row" style="display:flex; align-items:center; gap:13px; padding:13px 16px; text-decoration:none; color:#333;">
                    <svg style="width:20px; height:20px; color:#334155;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    <span style="font-size:0.74rem; font-weight:500;">Compare</span>
                </a>
                <a href="{{ route('track-order.index') }}" class="mm-row" style="display:flex; align-items:center; gap:13px; padding:13px 16px; text-decoration:none; color:#333;">
                    <svg style="width:20px; height:20px; color:#334155;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                    <span style="font-size:0.74rem; font-weight:500;">{{ sc('navbar', 'track_order', 'Track Order') }}</span>
                </a>
                <div class="mm-row" style="display:flex; align-items:center; justify-content:space-between; gap:10px; padding:11px 16px;">
                    <span style="display:flex; align-items:center; gap:13px; color:#333;">
                        <svg style="width:20px; height:20px; color:#334155;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0zM3.6 9h16.8M3.6 15h16.8M11.5 3a16 16 0 000 18M12.5 3a16 16 0 010 18"/></svg>
                        <span style="font-size:0.74rem; font-weight:500;">Language</span>
                    </span>
                    <span style="display:flex; align-items:center; gap:6px;">
                        <a href="{{ route('language.switch', 'en') }}" style="font-size:0.62rem; font-weight:700; padding:3px 10px; border-radius:999px; text-decoration:none; {{ $mmLocale === 'en' ? 'background:#f97316; color:#fff;' : 'background:#e5e7eb; color:#555;' }}">EN</a>
                        <a href="{{ route('language.switch', 'bn') }}" style="font-size:0.62rem; font-weight:700; padding:3px 10px; border-radius:999px; text-decoration:none; {{ $mmLocale === 'bn' ? 'background:#f97316; color:#fff;' : 'background:#e5e7eb; color:#555;' }}">বাং</a>
                    </span>
                </div>
            </div>

            <!-- Information -->
            <div style="padding:22px 18px 10px;">
                <span style="font-size:0.98rem; font-weight:800; color:#444;">Information</span>
                <div style="width:34px; height:3px; background:#f97316; border-radius:2px; margin-top:6px;"></div>
            </div>
            <div style="margin:0 14px; background:#f5f5f5; border-radius:12px; overflow:hidden;">
                <a href="{{ route('pages.about') }}" class="mm-row" style="display:flex; align-items:center; gap:13px; padding:13px 16px; text-decoration:none; color:#333;">
                    <svg style="width:20px; height:20px; color:#334155;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span style="font-size:0.74rem; font-weight:500;">About</span>
                </a>
                <a href="{{ route('services.index') }}" class="mm-row" style="display:flex; align-items:center; gap:13px; padding:13px 16px; text-decoration:none; color:#333;">
                    <svg style="width:20px; height:20px; color:#334155;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <span style="font-size:0.74rem; font-weight:500;">Services</span>
                </a>
                <a href="{{ route('blog.index') }}" class="mm-row" style="display:flex; align-items:center; gap:13px; padding:13px 16px; text-decoration:none; color:#333;">
                    <svg style="width:20px; height:20px; color:#334155;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    <span style="font-size:0.74rem; font-weight:500;">Blog</span>
                </a>
                <a href="{{ route('faq.index') }}" class="mm-row" style="display:flex; align-items:center; gap:13px; padding:13px 16px; text-decoration:none; color:#333;">
                    <svg style="width:20px; height:20px; color:#334155;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <span style="font-size:0.74rem; font-weight:500;">Faqs</span>
                </a>
                <a href="{{ route('contact.index') }}" class="mm-row" style="display:flex; align-items:center; gap:13px; padding:13px 16px; text-decoration:none; color:#333;">
                    <svg style="width:20px; height:20px; color:#334155;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span style="font-size:0.74rem; font-weight:500;">Contact</span>
                </a>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Sentinel: when this scrolls out of view, cat-bar becomes sticky -->
<div id="cat-bar-sentinel" style="height:0; pointer-events:none;"></div>

<!-- Dark category nav bar — outside x-data wrapper so position:sticky works relative to body -->
<div id="cat-bar" class="hidden lg:block" style="background:#0d1f2d; position:sticky; top:0; z-index:39; box-shadow:0 2px 8px rgba(0,0,0,0.2); transform:translateY(0); transition:box-shadow 0.3s ease, transform 0.3s ease;">
    <div class="container-custom px-4 sm:px-6 lg:px-8 flex items-center justify-between" style="height:54px;">
        <nav aria-label="Primary navigation" style="display:flex; align-items:center; overflow:visible;">
            @foreach($menuItems ?? [] as $item)
                @if($item->type === 'category' && $item->category)
                    @php $cat = $item->category; $children = $cat->children ?? collect(); @endphp
                    <div x-data="{ open: false, timer: null }" style="position:relative;"
                        @mouseenter="clearTimeout(timer); open = true"
                        @mouseleave="timer = setTimeout(() => open = false, 150)">
                        <a href="{{ route('categories.show', $cat) }}"
                            {{ $item->open_in_new_tab ? 'target=_blank rel=noopener' : '' }}
                            style="display:inline-flex; align-items:center; gap:3px; padding:0 14px; height:54px; color:rgba(255,255,255,0.75); font-size:0.875rem; font-weight:400; text-decoration:none; white-space:nowrap; transition:color 0.2s, background 0.2s; border-bottom:2px solid transparent;"
                            onmouseover="this.style.color='#fff'; this.style.background='rgba(255,255,255,0.08)'"
                            onmouseout="this.style.color='rgba(255,255,255,0.75)'; this.style.background='transparent'">
                            {{ $item->label }}
                            @if($children->count())
                                <i class="fi fi-rr-angle-small-down" style="font-size:12px; opacity:0.7;"></i>
                            @endif
                        </a>
                        @if($children->count())
                        <div x-show="open" class="nav-submenu"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            style="position:absolute; top:100%; left:0; min-width:210px; box-shadow:0 8px 24px rgba(0,0,0,0.12); z-index:100; border-radius:0 0 8px 8px; overflow:hidden;">
                            @foreach($children as $subCat)
                            <a href="{{ route('categories.show', $subCat) }}"
                                style="display:block; padding:9px 18px; font-size:0.875rem; text-decoration:none; border-bottom:1px solid rgba(0,0,0,0.05); transition:background 0.15s, color 0.15s;">
                                {{ $subCat->name }}
                            </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                @else
                    @php $isActive = request()->is(ltrim($item->url ?? '', '/')); @endphp
                    <a href="{{ $item->url ?? '#' }}"
                        {{ $item->open_in_new_tab ? 'target=_blank rel=noopener' : '' }}
                        style="display:inline-flex; align-items:center; padding:0 14px; height:54px; color:{{ $isActive ? '#fff' : 'rgba(255,255,255,0.75)' }}; font-size:0.875rem; font-weight:{{ $isActive ? '600' : '400' }}; text-decoration:none; white-space:nowrap; transition:color 0.2s, background 0.2s; border-bottom:{{ $isActive ? '2px solid #f97316' : '2px solid transparent' }};"
                        onmouseover="this.style.color='#fff'; this.style.background='rgba(255,255,255,0.08)'"
                        onmouseout="this.style.color='{{ $isActive ? '#fff' : 'rgba(255,255,255,0.75)' }}'; this.style.background='transparent'">
                        {{ $item->label }}
                    </a>
                @endif
            @endforeach
        </nav>
        <a href="{{ route('products.index') }}" style="display:inline-flex; align-items:center; gap:6px; padding:8px 18px; background:#f97316; color:#fff; border-radius:6px; font-size:0.8425rem; font-weight:700; text-decoration:none; letter-spacing:0.06em; flex-shrink:0; transition:background 0.2s; margin-left:16px;" onmouseover="this.style.background='#ea6c0a'" onmouseout="this.style.background='#f97316'">
            <svg style="width:13px; height:13px;" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
            FLASH SALE
        </a>
    </div>
</div>

<style>
.mobile-hamburger { display: none; }
/* Mobile drawer list rows — divider between items, none on the last */
.mm-row { border-bottom: 1px solid #e8e8e8; }
.mm-row:last-child { border-bottom: none; }
.mm-sub { border-bottom: 1px solid #ededed; }
.mm-sub:last-child { border-bottom: none; }
/* Hide the sidebar scrollbar (still scrollable) */
.mm-scroll { scrollbar-width: none; -ms-overflow-style: none; }
.mm-scroll::-webkit-scrollbar { width: 0; height: 0; display: none; }
@media (min-width: 1024px) {
    .lg-flex-col { display: flex !important; }
    .header-icon-label { display: block; }
}
@media (max-width: 1023px) {
    .header-icon-label { display: none; }

    /* ── Mobile header: hamburger left · logo center · cart right · search full-width below ── */
    .main-bar-inner   { flex-wrap: wrap; height: auto !important; gap: 6px 6px !important; padding-top: 6px !important; padding-bottom: 8px !important; }
    .mobile-hamburger { display: flex; order: 1; flex-shrink: 0; width: 36px; height: 36px; }
    .header-logo-link { order: 2; flex: 1 1 auto; justify-content: center; }
    #header-logo      { height: 32px !important; }
    .header-actions   { order: 3; flex-shrink: 0; }
    .header-actions > a, .header-actions > button { padding: 6px 8px !important; }
    .header-search    { order: 4; flex: 0 0 100% !important; max-width: 100% !important; margin: 0 !important; }
    /* Slimmer search field on mobile */
    .header-search [data-search-input] { padding-top: 5px !important; padding-bottom: 5px !important; font-size: 0.8125rem !important; }
    /* Wishlist & Compare live in the bottom nav on mobile — keep the top bar to just the cart */
    .header-action-wishlist, .header-action-compare { display: none !important; }
}
@keyframes catBarSlideIn {
    from { transform: translateY(-100%); opacity: 0; }
    to   { transform: translateY(0);    opacity: 1; }
}
#cat-bar.is-stuck {
    animation: catBarSlideIn 0.28s ease forwards;
    box-shadow: 0 4px 16px rgba(0,0,0,0.3) !important;
}
</style>

<script>
(function () {
    var sentinel = document.getElementById('cat-bar-sentinel');
    var catBar   = document.getElementById('cat-bar');
    if (!sentinel || !catBar) return;

    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (!entry.isIntersecting) {
                catBar.classList.add('is-stuck');
            } else {
                catBar.classList.remove('is-stuck');
            }
        });
    }, { threshold: 0, rootMargin: '0px' });

    observer.observe(sentinel);
})();
</script>

