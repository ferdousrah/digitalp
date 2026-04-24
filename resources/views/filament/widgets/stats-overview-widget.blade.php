<x-filament-widgets::widget>
    <div class="ds-stats-grid" style="display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:1rem;">
        @foreach($cards as $card)
            @php
                $featured = $card['featured'] ?? false;
                $trend = $card['trend'] ?? null;
                $trendInvert = $card['trendInvert'] ?? false;

                // For "pending" we invert so lower = good (green), higher = bad (red)
                $trendIsPositive = $trend === null ? null : ($trendInvert ? $trend < 0 : $trend > 0);

                // Featured = dark gradient (same in light/dark mode).
                // Non-featured = classes that auto-adapt to dark mode.
                $bg     = $featured
                    ? 'background:linear-gradient(135deg, #0b1220 0%, #0d1f2d 55%, #064e3b 100%); color:#fff; border:1px solid rgba(255,255,255,0.08); box-shadow:0 10px 30px -10px rgba(5, 150, 105, 0.4);'
                    : 'box-shadow:0 1px 3px rgba(0,0,0,0.04);';
                $cardClass    = $featured ? 'ds-stat-card ds-featured' : 'ds-stat-card';
                $labelColor   = $featured ? 'rgba(255,255,255,0.75)' : null; // null → use ds-text-muted class
                $valueColor   = $featured ? '#fff' : null;                   // null → use ds-text-strong class
                $dividerColor = $featured ? 'rgba(255,255,255,0.12)' : null; // null → use ds-divider class
                $subColor     = $featured ? 'rgba(255,255,255,0.65)' : null; // null → use ds-text-muted class
            @endphp

            <a href="{{ $card['href'] }}" class="{{ $cardClass }}" style="
                display:flex;
                flex-direction:column;
                border-radius:1rem;
                padding:1.25rem 1.25rem 0;
                text-decoration:none;
                transition:transform 0.25s cubic-bezier(.4,0,.2,1), box-shadow 0.25s ease;
                position:relative;
                overflow:hidden;
                {!! $bg !!}
            ">
                {{-- Top row: label + icon --}}
                <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:0.75rem; margin-bottom:0.5rem;">
                    <div @class(['ds-text-muted' => !$featured]) style="font-size:0.78rem; font-weight:600; letter-spacing:0.02em; {{ $labelColor ? 'color:' . $labelColor . ';' : '' }}">
                        {{ $card['label'] }}
                    </div>
                    <div style="width:44px; height:44px; flex:0 0 44px; border-radius:50%; background:{{ $card['iconBg'] }}; color:{{ $card['iconColor'] }}; display:flex; align-items:center; justify-content:center;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" style="width:22px; height:22px;">
                            {!! $card['icon'] !!}
                        </svg>
                    </div>
                </div>

                {{-- Value + trend --}}
                <div style="display:flex; align-items:baseline; gap:0.625rem; flex-wrap:wrap;">
                    <div @class(['ds-text-strong' => !$featured]) style="font-size:1.75rem; font-weight:700; letter-spacing:-0.025em; line-height:1.1; {{ $valueColor ? 'color:' . $valueColor . ';' : '' }}">
                        {{ $card['value'] }}
                    </div>

                    @if($trend !== null)
                        @php
                            $pillBg = $trendIsPositive
                                ? ($featured ? 'rgba(52, 211, 153, 0.2)' : '#dcfce7')
                                : ($featured ? 'rgba(248, 113, 113, 0.2)' : '#fee2e2');
                            $pillColor = $trendIsPositive
                                ? ($featured ? '#86efac' : '#15803d')
                                : ($featured ? '#fca5a5' : '#b91c1c');
                        @endphp
                        <div style="display:inline-flex; align-items:center; gap:0.25rem; padding:0.15rem 0.5rem; background:{{ $pillBg }}; color:{{ $pillColor }}; border-radius:999px; font-size:0.72rem; font-weight:600;">
                            @if($trendIsPositive)
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:11px; height:11px;">
                                    <path fill-rule="evenodd" d="M12 20.25a.75.75 0 0 1-.75-.75V6.31l-5.47 5.47a.75.75 0 1 1-1.06-1.06l6.75-6.75a.75.75 0 0 1 1.06 0l6.75 6.75a.75.75 0 1 1-1.06 1.06l-5.47-5.47V19.5a.75.75 0 0 1-.75.75Z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:11px; height:11px;">
                                    <path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v13.19l5.47-5.47a.75.75 0 1 1 1.06 1.06l-6.75 6.75a.75.75 0 0 1-1.06 0l-6.75-6.75a.75.75 0 1 1 1.06-1.06l5.47 5.47V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" />
                                </svg>
                            @endif
                            <span>{{ abs($trend) }}%</span>
                        </div>
                    @endif
                </div>

                {{-- Footer: last month --}}
                <div @class(['ds-divider ds-text-muted' => !$featured]) style="margin-top:1.1rem; padding:0.7rem 0 0.9rem; font-size:0.72rem; {{ $dividerColor ? 'border-top:1px solid ' . $dividerColor . ';' : '' }} {{ $subColor ? 'color:' . $subColor . ';' : '' }}">
                    {{ $card['lastLabel'] }}:
                    <strong @class(['ds-text-strong' => !$featured]) style="{{ $featured ? 'color:rgba(255,255,255,0.85);' : '' }}">{{ $card['lastValue'] }}</strong>
                </div>
            </a>
        @endforeach
    </div>

    <style>
        .ds-stat-card:hover {
            transform:translateY(-3px);
            box-shadow:0 12px 24px -8px rgba(0,0,0,0.10), 0 4px 8px -2px rgba(0,0,0,0.04);
        }
        @media (max-width: 900px) {
            .ds-stats-grid {
                grid-template-columns:repeat(2, minmax(0, 1fr)) !important;
            }
        }
        @media (max-width: 480px) {
            .ds-stats-grid {
                grid-template-columns:1fr !important;
            }
        }
    </style>
</x-filament-widgets::widget>
