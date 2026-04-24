@php
    // SVG half-donut math:
    // We draw a half circle arc from (20, 110) to (180, 110) over a center at (100, 110), radius 80.
    // Total arc length for 180°: PI * r = PI * 80 ≈ 251.33
    $radius = 80;
    $circumference = M_PI * $radius;               // half circle length
    $offset = $circumference * (1 - $fill / 100);   // dash-offset for fill%
    $isPositive = $pct >= 0;
@endphp

<x-filament-widgets::widget>
    <div class="ds-surface" style="border-radius:1rem; padding:1.5rem; box-shadow:0 1px 3px rgba(0,0,0,0.04); height:100%; display:flex; flex-direction:column;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
            <h3 style="margin:0; font-size:1.05rem; font-weight:700; letter-spacing:-0.01em;">User Growth</h3>

            <div x-data="{ open: false }" style="position:relative;">
                <button @click="open = !open" type="button" class="ds-chip"
                    style="display:inline-flex; align-items:center; gap:0.375rem; padding:0.375rem 0.75rem; border-radius:0.5rem; font-size:0.78rem; font-weight:500; cursor:pointer;">
                    <span>{{ $currentLabel }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:12px; height:12px;"><path fill-rule="evenodd" d="M12.53 16.28a.75.75 0 0 1-1.06 0l-7.5-7.5a.75.75 0 0 1 1.06-1.06L12 14.69l6.97-6.97a.75.75 0 1 1 1.06 1.06l-7.5 7.5Z" clip-rule="evenodd"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-cloak class="ds-surface"
                    style="position:absolute; top:calc(100% + 4px); right:0; border-radius:0.5rem; box-shadow:0 8px 20px -4px rgba(0,0,0,0.1); padding:0.25rem; min-width:140px; z-index:10;">
                    @foreach(['week' => 'This Week', 'month' => 'This Month', 'year' => 'This Year'] as $key => $label)
                        <button type="button" wire:click="setCompare('{{ $key }}')" @click="open = false"
                            style="display:block; width:100%; text-align:left; padding:0.5rem 0.75rem; background:{{ $compareTo === $key ? '#ecfdf5' : 'transparent' }}; color:{{ $compareTo === $key ? '#059669' : '#475569' }}; border:none; border-radius:0.375rem; font-size:0.82rem; font-weight:500; cursor:pointer;"
                            onmouseover="this.style.background='#f1f5f9'"
                            onmouseout="this.style.background='{{ $compareTo === $key ? '#ecfdf5' : 'transparent' }}'">{{ $label }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Half-donut gauge --}}
        <div style="flex:1; display:flex; align-items:center; justify-content:center; padding:1rem 0;">
            <div style="position:relative; width:260px; max-width:100%;">
                <svg viewBox="0 0 200 130" xmlns="http://www.w3.org/2000/svg" style="display:block; width:100%; height:auto;">
                    {{-- Track (background arc) — class so dark mode can restyle --}}
                    <path d="M 20 110 A 80 80 0 0 1 180 110"
                        fill="none"
                        class="ds-gauge-track"
                        stroke-width="20"
                        stroke-linecap="round" />

                    {{-- Fill (colored arc) --}}
                    <path d="M 20 110 A 80 80 0 0 1 180 110"
                        fill="none"
                        stroke="{{ $isPositive ? '#86c267' : '#f87171' }}"
                        stroke-width="20"
                        stroke-linecap="round"
                        stroke-dasharray="{{ $circumference }}"
                        stroke-dashoffset="{{ $offset }}"
                        style="transition:stroke-dashoffset 0.8s cubic-bezier(.4,0,.2,1);" />
                </svg>

                {{-- Center label --}}
                <div style="position:absolute; left:0; right:0; bottom:15%; text-align:center;">
                    <div class="ds-text-strong" style="font-size:2rem; font-weight:700; letter-spacing:-0.025em; line-height:1;">
                        {{ number_format($current) }}
                    </div>
                    <div style="margin-top:0.375rem; display:inline-flex; align-items:center; gap:0.25rem; font-size:0.82rem; font-weight:600; color:{{ $isPositive ? '#15803d' : '#b91c1c' }};">
                        @if($isPositive)
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:12px; height:12px;"><path fill-rule="evenodd" d="M12 20.25a.75.75 0 0 1-.75-.75V6.31l-5.47 5.47a.75.75 0 1 1-1.06-1.06l6.75-6.75a.75.75 0 0 1 1.06 0l6.75 6.75a.75.75 0 1 1-1.06 1.06l-5.47-5.47V19.5a.75.75 0 0 1-.75.75Z" clip-rule="evenodd" /></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:12px; height:12px;"><path fill-rule="evenodd" d="M12 3.75a.75.75 0 0 1 .75.75v13.19l5.47-5.47a.75.75 0 1 1 1.06 1.06l-6.75 6.75a.75.75 0 0 1-1.06 0l-6.75-6.75a.75.75 0 1 1 1.06-1.06l5.47 5.47V4.5a.75.75 0 0 1 .75-.75Z" clip-rule="evenodd" /></svg>
                        @endif
                        <span>{{ $isPositive ? '+' : '' }}{{ $pct }}%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer comparison row --}}
        <div class="ds-divider" style="display:grid; grid-template-columns:1fr 1fr; gap:0.75rem; padding-top:1rem;">
            <div class="ds-surface-muted" style="padding:0.5rem 0.75rem; border-radius:0.5rem;">
                <div class="ds-text-muted" style="font-size:0.7rem; letter-spacing:0.02em; margin-bottom:0.125rem;">{{ $currentLabel }}</div>
                <div class="ds-text-strong" style="font-size:1rem; font-weight:700;">{{ number_format($current) }}</div>
            </div>
            <div class="ds-surface-muted" style="padding:0.5rem 0.75rem; border-radius:0.5rem;">
                <div class="ds-text-muted" style="font-size:0.7rem; letter-spacing:0.02em; margin-bottom:0.125rem;">{{ $prevLabel }}</div>
                <div class="ds-text-strong" style="font-size:1rem; font-weight:700;">{{ number_format($previous) }}</div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
