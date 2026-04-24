@php
    $cards = [
        [
            'label' => 'Orders today',
            'value' => $ordersToday,
            'color' => '#34d399',
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/></svg>',
            'href'  => url('/admin/orders'),
        ],
        [
            'label' => 'Pending',
            'value' => $pendingOrders,
            'color' => '#fbbf24',
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>',
            'href'  => url('/admin/orders?tableFilters[status][value]=pending'),
        ],
        [
            'label' => 'Messages',
            'value' => $newContacts,
            'color' => '#60a5fa',
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/></svg>',
            'href'  => url('/admin/contact-submissions'),
        ],
        [
            'label' => 'Low stock',
            'value' => $lowStock,
            'color' => '#f87171',
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>',
            'href'  => url('/admin/products?tableFilters[low_stock][value]=1'),
        ],
    ];

    $quickLinks = [
        ['label' => 'New Product',  'href' => url('/admin/products/create'),  'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>'],
        ['label' => 'New Order',    'href' => url('/admin/orders/create'),    'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/>'],
        ['label' => 'Categories',   'href' => url('/admin/categories'),       'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6Z"/>'],
        ['label' => 'Settings',     'href' => url('/admin/settings'),         'icon' => '<path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>'],
    ];
@endphp

<x-filament-widgets::widget>
    <div class="ds-hero" style="
        position:relative;
        border-radius:1rem;
        overflow:hidden;
        background:
            radial-gradient(circle at 80% 20%, rgba(16,185,129,0.35) 0%, transparent 45%),
            radial-gradient(circle at 10% 90%, rgba(59,130,246,0.25) 0%, transparent 45%),
            linear-gradient(135deg, #0b1220 0%, #0d1f2d 55%, #064e3b 100%);
        color:#fff;
        padding:1.75rem 2rem;
        box-shadow:0 20px 40px -20px rgba(5, 150, 105, 0.4), 0 10px 30px -10px rgba(0,0,0,0.3);
        border:1px solid rgba(255,255,255,0.06);
    ">
        {{-- Animated shine sweep --}}
        <div class="ds-hero-shine" aria-hidden="true" style="
            position:absolute; top:-50%; left:-100%; width:50%; height:200%;
            background:linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.05) 50%, transparent 100%);
            transform:skewX(-20deg);
            animation:ds-shine 8s ease-in-out infinite;
            pointer-events:none;
        "></div>

        <div style="position:relative; display:grid; grid-template-columns:1fr 1.4fr; gap:2rem; align-items:stretch;">

            {{-- LEFT: Greeting + Quick links --}}
            <div style="display:flex; flex-direction:column; justify-content:space-between; gap:1.25rem; min-width:0;">
                <div>
                    <div style="display:inline-flex; align-items:center; gap:0.5rem; padding:0.25rem 0.75rem; background:rgba(255,255,255,0.08); backdrop-filter:blur(10px); border:1px solid rgba(255,255,255,0.12); border-radius:100px; font-size:0.72rem; font-weight:500; letter-spacing:0.04em; margin-bottom:0.75rem;">
                        <span style="width:6px; height:6px; border-radius:50%; background:#34d399; box-shadow:0 0 8px #34d399; animation:ds-pulse 2s ease-in-out infinite;"></span>
                        <span style="opacity:0.9;">{{ $today }}</span>
                    </div>
                    <h2 style="margin:0 0 0.4rem; font-size:1.75rem; font-weight:700; letter-spacing:-0.025em; line-height:1.15;">
                        {{ $greeting }},
                        <span style="background:linear-gradient(90deg, #86efac, #34d399); -webkit-background-clip:text; background-clip:text; color:transparent;">{{ $userName }}</span>
                    </h2>
                    <p style="margin:0; font-size:0.875rem; opacity:0.7; max-width:44ch; line-height:1.5;">
                        Here's your store at a glance. Jump into any area with the quick links below.
                    </p>
                </div>

                {{-- Quick action chips --}}
                <div style="display:flex; gap:0.5rem; flex-wrap:wrap;">
                    @foreach($quickLinks as $link)
                        <a href="{{ $link['href'] }}" style="display:inline-flex; align-items:center; gap:0.4rem; padding:0.45rem 0.85rem; background:rgba(255,255,255,0.07); border:1px solid rgba(255,255,255,0.12); border-radius:0.5rem; font-size:0.78rem; font-weight:500; color:#fff; text-decoration:none; transition:all 0.2s;"
                            onmouseover="this.style.background='rgba(255,255,255,0.15)'; this.style.transform='translateY(-1px)';"
                            onmouseout="this.style.background='rgba(255,255,255,0.07)'; this.style.transform='translateY(0)';">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" style="width:14px; height:14px; opacity:0.8;">{!! $link['icon'] !!}</svg>
                            <span>{{ $link['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- RIGHT: Stat tiles --}}
            <div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:0.75rem;">
                @foreach($cards as $c)
                    <a href="{{ $c['href'] }}" class="ds-stat-tile" style="
                        display:flex;
                        align-items:center;
                        gap:0.875rem;
                        background:rgba(255,255,255,0.05);
                        backdrop-filter:blur(12px);
                        border:1px solid rgba(255,255,255,0.08);
                        border-radius:0.75rem;
                        padding:0.9rem 1rem;
                        text-decoration:none;
                        color:inherit;
                        transition:all 0.25s cubic-bezier(.4,0,.2,1);
                    "
                    onmouseover="this.style.background='rgba(255,255,255,0.1)'; this.style.transform='translateY(-2px)'; this.style.borderColor='rgba(255,255,255,0.18)';"
                    onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.transform='translateY(0)'; this.style.borderColor='rgba(255,255,255,0.08)';">

                        <div style="width:40px; height:40px; flex:0 0 40px; border-radius:0.625rem; background:{{ $c['color'] }}22; color:{{ $c['color'] }}; display:flex; align-items:center; justify-content:center; box-shadow:inset 0 0 0 1px {{ $c['color'] }}33;">
                            <span style="width:20px; height:20px; display:block;">{!! $c['icon'] !!}</span>
                        </div>

                        <div style="min-width:0; flex:1;">
                            <div style="font-size:0.68rem; font-weight:500; letter-spacing:0.06em; text-transform:uppercase; opacity:0.65; margin-bottom:0.125rem;">{{ $c['label'] }}</div>
                            <div style="font-size:1.55rem; font-weight:700; letter-spacing:-0.02em; line-height:1.1;">{{ number_format($c['value']) }}</div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>

    <style>
        @keyframes ds-shine {
            0%, 100% { left:-100%; }
            60%, 100% { left:200%; }
        }
        @keyframes ds-pulse {
            0%, 100% { opacity:1; transform:scale(1); }
            50% { opacity:0.5; transform:scale(1.2); }
        }

        @media (max-width: 900px) {
            .ds-hero > div:first-of-type {
                grid-template-columns:1fr !important;
            }
        }
        @media (max-width: 500px) {
            .ds-hero > div:first-of-type > div:last-child {
                grid-template-columns:1fr !important;
            }
        }
    </style>
</x-filament-widgets::widget>
