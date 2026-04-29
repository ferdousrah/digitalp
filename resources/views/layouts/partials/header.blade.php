<div x-data="{ mobileMenuOpen: false, megaOpen: false, megaTimer: null, mobileProductsOpen: false }"
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
        <div class="container-custom flex items-center gap-4 px-4 sm:px-6 lg:px-8" style="height:92px; padding-top:10px; padding-bottom:10px; transition:height 0.3s ease;">

            <!-- Logo -->
            @php
                $siteLogo = \App\Services\SettingService::get('site_logo');
                $siteName = \App\Services\SettingService::get('site_name', config('app.name'));
                // Split site_name on first space — first word in primary, rest in accent.
                // For one-word names ("Qloraa"), only the primary span renders.
                $nameParts = preg_split('/\s+/', trim($siteName), 2);
            @endphp
            <a href="{{ route('home') }}" aria-label="{{ $siteName }}" style="flex-shrink:0; display:flex; align-items:center; text-decoration:none;">
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
            <div style="flex:1; max-width:640px; margin:0 auto;">
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
            <div style="display:flex; align-items:center; flex-shrink:0;">

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
                <a href="{{ route('wishlist.index') }}" style="display:flex; flex-direction:column; align-items:center; gap:2px; padding:8px 10px; color:#374151; text-decoration:none; transition:color 0.2s; position:relative;" onmouseover="this.style.color='#ef4444'" onmouseout="this.style.color='#374151'" aria-label="Wishlist">
                    <i class="fi fi-rr-heart" style="font-size:22px; line-height:1;"></i>
                    <span class="header-icon-label" style="font-size:0.7125rem; font-weight:500;">{{ sc('navbar', 'wishlist', 'Wishlist') }}</span>
                    <span id="wishlist-badge" style="position:absolute; top:2px; right:4px; background:#ef4444; color:#fff; font-size:0.6625rem; font-weight:700; min-width:16px; height:16px; border-radius:50%; display:{{ ($wishlistCount ?? 0) > 0 ? 'flex' : 'none' }}; align-items:center; justify-content:center; line-height:1;">{{ $wishlistCount ?? 0 }}</span>
                </a>

                <!-- Compare -->
                <a href="{{ route('compare.index') }}" style="display:flex; flex-direction:column; align-items:center; gap:2px; padding:8px 10px; color:#374151; text-decoration:none; transition:color 0.2s; position:relative;" onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='#374151'" aria-label="Compare">
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

                <!-- Mobile hamburger -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="lg:hidden" style="display:flex; flex-direction:column; align-items:center; gap:2px; padding:8px 10px; color:#374151; background:none; border:none; cursor:pointer; transition:color 0.2s;" onmouseover="this.style.color='#16a34a'" onmouseout="this.style.color='#374151'" aria-label="Menu">
                    <i x-show="!mobileMenuOpen" class="fi fi-rr-menu-burger" style="font-size:22px; line-height:1;"></i>
                    <i x-show="mobileMenuOpen" class="fi fi-rr-cross" style="font-size:22px; line-height:1;"></i>
                    <span style="font-size:0.7125rem; font-weight:500;">More</span>
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
        style="position:absolute; top:0; left:0; bottom:0; width:320px; max-width:85vw; background:#fff; box-shadow:4px 0 24px rgba(0,0,0,0.15); display:flex; flex-direction:column;">

        <!-- Sidebar Header -->
        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid #e5e7eb;">
            <a href="{{ route('home') }}" aria-label="{{ $siteName }}" style="display:flex; align-items:center; gap:6px; text-decoration:none;">
                @if($siteLogo ?? false)
                    <img src="{{ Storage::disk('public')->url($siteLogo) }}" alt="{{ $siteName }}" decoding="async" style="height:36px; width:auto;">
                @else
                    <span style="font-weight:800; font-size:1.1rem; color:#16a34a;">{{ $nameParts[0] }}</span>
                    @if(!empty($nameParts[1]))
                        <span style="font-weight:800; font-size:1.1rem; color:#ef4444;">{{ $nameParts[1] }}</span>
                    @endif
                @endif
            </a>
            <button @click="mobileMenuOpen = false" aria-label="Close menu" style="width:36px; height:36px; display:flex; align-items:center; justify-content:center; border-radius:50%; background:#f3f4f6; border:none; cursor:pointer; color:#374151; transition:background 0.2s;" onmouseover="this.style.background='#e5e7eb'" onmouseout="this.style.background='#f3f4f6'">
                <svg style="width:20px; height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Sidebar Body -->
        <div style="flex:1; overflow-y:auto; padding:12px 0;">
            <div style="padding:0 20px;">
                <a href="{{ route('home') }}" style="display:flex; align-items:center; gap:12px; padding:12px 0; text-decoration:none; color:{{ request()->routeIs('home') ? '#16a34a' : '#111827' }}; font-weight:600; font-size:1rem; border-bottom:1px solid #f3f4f6;">
                    <svg style="width:20px; height:20px; color:{{ request()->routeIs('home') ? '#16a34a' : '#9ca3af' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Home
                </a>

                <!-- Products accordion -->
                <div style="border-bottom:1px solid #f3f4f6;" x-data="{ open: false }">
                    <div style="display:flex; align-items:center;">
                        <a href="{{ route('products.index') }}" style="flex:1; display:flex; align-items:center; gap:12px; padding:12px 0; text-decoration:none; color:{{ request()->routeIs('products.*') || request()->routeIs('categories.*') ? '#16a34a' : '#111827' }}; font-weight:600; font-size:1rem;">
                            <svg style="width:20px; height:20px; color:{{ request()->routeIs('products.*') || request()->routeIs('categories.*') ? '#16a34a' : '#9ca3af' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                            Products
                        </a>
                        <button @click="open = !open" aria-label="Toggle category" :aria-expanded="open" style="width:36px; height:36px; display:flex; align-items:center; justify-content:center; border:none; background:transparent; cursor:pointer; color:#6b7280;">
                            <svg style="width:18px; height:18px; transition:transform 0.3s;" :style="open && 'transform:rotate(180deg)'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                    <div x-show="open" x-collapse>
                        <div style="padding:0 0 12px 32px;" x-data="{ openCat: null }">
                            @foreach($megaCategories as $megaCat)
                            <div style="border-bottom:1px solid #f9fafb;">
                                @if($megaCat->children->count())
                                    <div style="display:flex; align-items:center;">
                                        <a href="{{ route('categories.show', $megaCat) }}" style="flex:1; display:flex; align-items:center; gap:10px; padding:8px 0; text-decoration:none; color:#374151; font-size:0.9625rem; font-weight:500;">
                                            @if($megaCat->getFirstMediaUrl('category_image'))
                                                <img src="{{ $megaCat->getFirstMediaUrl('category_image') }}" alt="" loading="lazy" decoding="async" width="28" height="28" style="width:28px; height:28px; border-radius:6px; object-fit:cover; border:1px solid #e5e7eb;">
                                            @else
                                                <span style="width:28px; height:28px; border-radius:6px; background:#f0fdf4; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                                    <svg style="width:14px; height:14px; color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                                </span>
                                            @endif
                                            {{ $megaCat->name }}
                                        </a>
                                        <button @click="openCat = openCat === {{ $megaCat->id }} ? null : {{ $megaCat->id }}" style="width:30px; height:30px; display:flex; align-items:center; justify-content:center; border:none; background:transparent; cursor:pointer; color:#9ca3af; flex-shrink:0;">
                                            <svg style="width:14px; height:14px; transition:transform 0.3s;" :style="openCat === {{ $megaCat->id }} && 'transform:rotate(180deg)'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                    </div>
                                    <div x-show="openCat === {{ $megaCat->id }}" x-collapse>
                                        <div style="padding:2px 0 8px 38px;">
                                            @foreach($megaCat->children as $subCat)
                                                <a href="{{ route('categories.show', $subCat) }}" style="display:block; padding:5px 0; font-size:0.8875rem; color:#6b7280; text-decoration:none;">{{ $subCat->name }}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                @else
                                    <a href="{{ route('categories.show', $megaCat) }}" style="display:flex; align-items:center; gap:10px; padding:8px 0; text-decoration:none; color:#374151; font-size:0.9625rem; font-weight:500;">
                                        @if($megaCat->getFirstMediaUrl('category_image'))
                                            <img src="{{ $megaCat->getFirstMediaUrl('category_image') }}" alt="" loading="lazy" decoding="async" width="28" height="28" style="width:28px; height:28px; border-radius:6px; object-fit:cover; border:1px solid #e5e7eb;">
                                        @else
                                            <span style="width:28px; height:28px; border-radius:6px; background:#f0fdf4; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                                <svg style="width:14px; height:14px; color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                                            </span>
                                        @endif
                                        {{ $megaCat->name }}
                                    </a>
                                @endif
                            </div>
                            @endforeach
                            <a href="{{ route('products.index') }}" style="display:flex; align-items:center; gap:6px; padding:10px 0 4px; font-size:0.85rem; color:#16a34a; font-weight:600; text-decoration:none;">
                                View All Products
                                <svg style="width:14px; height:14px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                            </a>
                        </div>
                    </div>
                </div>

                <a href="{{ route('services.index') }}" style="display:flex; align-items:center; gap:12px; padding:12px 0; text-decoration:none; color:{{ request()->routeIs('services.*') ? '#16a34a' : '#111827' }}; font-weight:600; font-size:1rem; border-bottom:1px solid #f3f4f6;">
                    <svg style="width:20px; height:20px; color:{{ request()->routeIs('services.*') ? '#16a34a' : '#9ca3af' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Services
                </a>
                <a href="{{ route('blog.index') }}" style="display:flex; align-items:center; gap:12px; padding:12px 0; text-decoration:none; color:{{ request()->routeIs('blog.*') ? '#16a34a' : '#111827' }}; font-weight:600; font-size:1rem; border-bottom:1px solid #f3f4f6;">
                    <svg style="width:20px; height:20px; color:{{ request()->routeIs('blog.*') ? '#16a34a' : '#9ca3af' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    Blog
                </a>
                <a href="{{ route('pages.about') }}" style="display:flex; align-items:center; gap:12px; padding:12px 0; text-decoration:none; color:{{ request()->routeIs('pages.about') ? '#16a34a' : '#111827' }}; font-weight:600; font-size:1rem; border-bottom:1px solid #f3f4f6;">
                    <svg style="width:20px; height:20px; color:{{ request()->routeIs('pages.about') ? '#16a34a' : '#9ca3af' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    About
                </a>
                <a href="{{ route('contact.index') }}" style="display:flex; align-items:center; gap:12px; padding:12px 0; text-decoration:none; color:{{ request()->routeIs('contact.*') ? '#16a34a' : '#111827' }}; font-weight:600; font-size:1rem; border-bottom:1px solid #f3f4f6;">
                    <svg style="width:20px; height:20px; color:{{ request()->routeIs('contact.*') ? '#16a34a' : '#9ca3af' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    Contact
                </a>
            </div>

            <div style="height:8px; background:#f3f4f6; margin:12px 0;"></div>

            <div style="padding:0 20px;">
                <a href="{{ route('wishlist.index') }}" style="display:flex; align-items:center; justify-content:space-between; padding:12px 0; text-decoration:none; color:#111827; font-weight:500; font-size:0.9875rem; border-bottom:1px solid #f3f4f6;">
                    <span style="display:flex; align-items:center; gap:12px;">
                        <svg style="width:20px; height:20px; color:#ef4444;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        My Wishlist
                    </span>
                    @if(($wishlistCount ?? 0) > 0)
                        <span style="background:#fef2f2; color:#ef4444; font-size:0.8125rem; font-weight:700; padding:2px 10px; border-radius:12px;">{{ $wishlistCount }}</span>
                    @endif
                </a>
                <a href="{{ route('compare.index') }}" style="display:flex; align-items:center; justify-content:space-between; padding:12px 0; text-decoration:none; color:#111827; font-weight:500; font-size:0.9875rem; border-bottom:1px solid #f3f4f6;">
                    <span style="display:flex; align-items:center; gap:12px;">
                        <svg style="width:20px; height:20px; color:#16a34a;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        Compare Products
                    </span>
                    @if(($compareCount ?? 0) > 0)
                        <span style="background:#f0fdf4; color:#16a34a; font-size:0.8125rem; font-weight:700; padding:2px 10px; border-radius:12px;">{{ $compareCount }}</span>
                    @endif
                </a>
                <a href="{{ route('track-order.index') }}" style="display:flex; align-items:center; gap:12px; padding:12px 0; text-decoration:none; color:{{ request()->routeIs('track-order.*') ? '#f97316' : '#111827' }}; font-weight:500; font-size:0.9875rem;">
                    <i class="fi fi-rr-box-open" style="font-size:20px; color:{{ request()->routeIs('track-order.*') ? '#f97316' : '#9ca3af' }};"></i>
                    {{ sc('navbar', 'track_order', 'Track Order') }}
                </a>
            </div>
        </div>

        <!-- Sidebar Footer -->
        <div style="border-top:1px solid #e5e7eb; padding:16px 20px; background:#f9fafb;">
            <div style="display:flex; align-items:center; gap:8px; color:#6b7280; font-size:0.8875rem;">
                <svg style="width:16px; height:16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <span>Need help? <a href="{{ route('contact.index') }}" style="color:#16a34a; font-weight:600; text-decoration:none;">Contact Us</a></span>
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
@media (min-width: 1024px) {
    .lg-flex-col { display: flex !important; }
    .header-icon-label { display: block; }
}
@media (max-width: 1023px) {
    .header-icon-label { display: none; }
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

