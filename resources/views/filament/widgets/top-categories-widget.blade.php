@php
    // Bubble layout: max = 220px, scale others down by their ratio
    $MAX = 210; // px, biggest bubble diameter
    $MIN = 90;  // px, smallest allowed
@endphp

<x-filament-widgets::widget>
    <div class="ds-surface" style="border-radius:1rem; padding:1.5rem; box-shadow:0 1px 3px rgba(0,0,0,0.04); height:100%; display:flex; flex-direction:column;">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:0.75rem;">
            <h3 style="margin:0; font-size:1.05rem; font-weight:700; letter-spacing:-0.01em;">Top Categories</h3>

            <div x-data="{ open: false }" style="position:relative;">
                <button @click="open = !open" type="button" class="ds-chip"
                    style="display:inline-flex; align-items:center; gap:0.375rem; padding:0.375rem 0.75rem; border-radius:0.5rem; font-size:0.78rem; font-weight:500; cursor:pointer;">
                    <span>{{ ['week' => 'This Week', 'month' => 'This Month', 'year' => 'This Year'][$period] ?? 'This Week' }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width:12px; height:12px;"><path fill-rule="evenodd" d="M12.53 16.28a.75.75 0 0 1-1.06 0l-7.5-7.5a.75.75 0 0 1 1.06-1.06L12 14.69l6.97-6.97a.75.75 0 1 1 1.06 1.06l-7.5 7.5Z" clip-rule="evenodd"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-cloak class="ds-surface"
                    style="position:absolute; top:calc(100% + 4px); right:0; border-radius:0.5rem; box-shadow:0 8px 20px -4px rgba(0,0,0,0.1); padding:0.25rem; min-width:140px; z-index:10;">
                    @foreach(['week' => 'This Week', 'month' => 'This Month', 'year' => 'This Year'] as $key => $label)
                        <button type="button" wire:click="setPeriod('{{ $key }}')" @click="open = false"
                            style="display:block; width:100%; text-align:left; padding:0.5rem 0.75rem; background:{{ $period === $key ? '#ecfdf5' : 'transparent' }}; color:{{ $period === $key ? '#059669' : '#475569' }}; border:none; border-radius:0.375rem; font-size:0.82rem; font-weight:500; cursor:pointer; transition:background 0.15s;"
                            onmouseover="this.style.background='#f1f5f9'"
                            onmouseout="this.style.background='{{ $period === $key ? '#ecfdf5' : 'transparent' }}'">{{ $label }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Bubble cluster --}}
        @if(count($bubbles) === 0)
            <div class="ds-text-muted" style="flex:1; display:flex; align-items:center; justify-content:center; padding:3rem 1rem; font-size:0.9rem;">
                No paid orders yet in this period.
            </div>
        @else
            <div style="flex:1; position:relative; min-height:320px; display:flex; align-items:center; justify-content:center;">
                @foreach($bubbles as $i => $b)
                    @php
                        $diameter = max($MIN, ($b['size'] / 100) * $MAX);
                        // Position the 3 bubbles in an overlapping cluster
                        $positions = [
                            ['top' => '0',       'left' => '50%', 'transform' => 'translateX(-50%)'],
                            ['top' => '40%',     'left' => '10%', 'transform' => 'translate(0, 0)'],
                            ['top' => '38%',     'left' => 'auto', 'right' => '8%', 'transform' => 'translate(0, 0)'],
                        ];
                        $pos = $positions[$i] ?? $positions[0];
                    @endphp
                    <div style="
                        position:absolute;
                        top:{{ $pos['top'] }};
                        @isset($pos['left']) left:{{ $pos['left'] }}; @endisset
                        @isset($pos['right']) right:{{ $pos['right'] }}; @endisset
                        width:{{ $diameter }}px; height:{{ $diameter }}px;
                        background:{{ $b['bg'] }};
                        color:{{ $b['text'] }};
                        border-radius:50%;
                        display:flex; flex-direction:column; align-items:center; justify-content:center;
                        transform:{{ $pos['transform'] }};
                        box-shadow:0 8px 20px -6px {{ $b['bg'] }}66;
                        transition:transform 0.3s cubic-bezier(.4,0,.2,1);
                    "
                    onmouseover="this.style.transform='{{ $pos['transform'] }} scale(1.05)'"
                    onmouseout="this.style.transform='{{ $pos['transform'] }}'">
                        <div style="font-size:{{ round($diameter * 0.14) }}px; font-weight:700; letter-spacing:-0.02em; line-height:1;">{{ $b['revenue'] }}</div>
                        <div style="font-size:{{ round($diameter * 0.085) }}px; opacity:0.85; margin-top:0.25rem;">{{ $b['name'] }}</div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-widgets::widget>
