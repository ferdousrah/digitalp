@php
    // SVG half-donut: radius 80, arc length = PI * 80
    $radius = 80;
    $circumference = M_PI * $radius;

    // Calculate cumulative stroke offsets for each segment in $topForArc.
    // We show the top 4 sources stacked as colored slices along the arc, each sized
    // by their percent of total — remainder fills out any slack as light gray.
    $segments = [];
    $consumedPct = 0;
    foreach ($topForArc as $i => $s) {
        $len = ($s['percent'] / 100) * $circumference;
        $offsetStart = ($consumedPct / 100) * $circumference;
        $segments[] = [
            'color'  => $s['color'],
            // dasharray describes: drawn length, gap length
            'dasharray' => "$len " . ($circumference - $len),
            'dashoffset' => -$offsetStart,
        ];
        $consumedPct += $s['percent'];
    }
@endphp

<x-filament-widgets::widget>
    <div class="ds-surface" style="border-radius:1rem; padding:1.5rem; box-shadow:0 1px 3px rgba(0,0,0,0.04); height:100%; display:flex; flex-direction:column;">

        {{-- Header --}}
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.5rem;">
            <h3 style="margin:0; font-size:1.05rem; font-weight:700; letter-spacing:-0.01em;">Total Visitors</h3>

            <div x-data="{ open: false }" style="position:relative;">
                <button @click="open = !open" type="button" class="ds-chip"
                    style="display:inline-flex; align-items:center; gap:0.375rem; padding:0.375rem 0.75rem; border-radius:0.5rem; font-size:0.78rem; font-weight:500; cursor:pointer;">
                    <span>{{ $periodLabel }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:12px; height:12px;"><path fill-rule="evenodd" d="M12.53 16.28a.75.75 0 0 1-1.06 0l-7.5-7.5a.75.75 0 0 1 1.06-1.06L12 14.69l6.97-6.97a.75.75 0 1 1 1.06 1.06l-7.5 7.5Z" clip-rule="evenodd"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-cloak class="ds-surface"
                    style="position:absolute; top:calc(100% + 4px); right:0; border-radius:0.5rem; box-shadow:0 8px 20px -4px rgba(0,0,0,0.1); padding:0.25rem; min-width:150px; z-index:10;">
                    @foreach(['today' => 'Today', 'week' => 'This Week', 'month' => 'This Month', 'year' => 'This Year'] as $key => $label)
                        <button type="button" wire:click="setVisitorPeriod('{{ $key }}')" @click="open = false"
                            style="display:block; width:100%; text-align:left; padding:0.5rem 0.75rem; background:{{ $period === $key ? '#ecfdf5' : 'transparent' }}; color:{{ $period === $key ? '#059669' : '' }}; border:none; border-radius:0.375rem; font-size:0.82rem; font-weight:500; cursor:pointer;"
                            onmouseover="this.style.background='{{ $period === $key ? '#ecfdf5' : '#f1f5f9' }}'"
                            onmouseout="this.style.background='{{ $period === $key ? '#ecfdf5' : 'transparent' }}'">{{ $label }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Half-donut gauge --}}
        <div style="display:flex; align-items:center; justify-content:center; padding:0.5rem 0;">
            <div style="position:relative; width:240px; max-width:100%;">
                <svg viewBox="0 0 200 130" xmlns="http://www.w3.org/2000/svg" style="display:block; width:100%; height:auto;">
                    {{-- Track --}}
                    <path d="M 20 110 A 80 80 0 0 1 180 110"
                        fill="none"
                        class="ds-gauge-track"
                        stroke-width="18"
                        stroke-linecap="round" />

                    {{-- Stacked segments (drawn one by one using dasharray + offset) --}}
                    @foreach($segments as $seg)
                        <path d="M 20 110 A 80 80 0 0 1 180 110"
                            fill="none"
                            stroke="{{ $seg['color'] }}"
                            stroke-width="18"
                            stroke-linecap="butt"
                            stroke-dasharray="{{ $seg['dasharray'] }}"
                            stroke-dashoffset="{{ $seg['dashoffset'] }}"
                            style="transition:all 0.6s cubic-bezier(.4,0,.2,1);" />
                    @endforeach
                </svg>

                {{-- Center percent label (shows the top source's share) --}}
                <div style="position:absolute; left:0; right:0; bottom:8%; text-align:center;">
                    <div class="ds-text-strong" style="font-size:2rem; font-weight:800; letter-spacing:-0.025em; line-height:1;">
                        {{ $bigPercent }}<span style="font-size:1.25rem; font-weight:700;">%</span>
                    </div>
                    <div class="ds-text-muted" style="font-size:0.72rem; margin-top:0.25rem; letter-spacing:0.02em;">
                        {{ number_format($total) }} total visits
                    </div>
                </div>
            </div>
        </div>

        {{-- Source breakdown list --}}
        <div style="display:flex; flex-direction:column; gap:0.5rem; padding-top:0.25rem;">
            @if(count($sources) === 0)
                <div class="ds-text-muted" style="padding:1rem; text-align:center; font-size:0.85rem;">
                    No visitors in this period yet.
                </div>
            @else
                @foreach($sources as $s)
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:0.75rem; padding:0.3rem 0;">
                        <div style="display:flex; align-items:center; gap:0.5rem; min-width:0; flex:1;">
                            <span style="width:7px; height:7px; border-radius:50%; background:{{ $s['color'] }}; box-shadow:0 0 0 2px {{ $s['color'] }}22; flex-shrink:0;"></span>
                            <span class="ds-text-strong" style="font-size:0.85rem; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">{{ $s['label'] }}</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:0.5rem; flex-shrink:0;">
                            <span class="ds-text-muted" style="font-size:0.75rem;">{{ number_format($s['visits']) }}</span>
                            <span class="ds-text-strong" style="font-size:0.85rem; font-weight:700; min-width:2.5rem; text-align:right;">{{ $s['percent'] }}%</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</x-filament-widgets::widget>
