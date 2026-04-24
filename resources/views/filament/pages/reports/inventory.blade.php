<x-filament-panels::page>
    @php
        $r = $this->getReport();
        $fmt = fn ($v) => '৳' . number_format($v, 2);
    @endphp

    <div class="ds-surface" style="padding:1.25rem 1.5rem; border-radius:1rem; margin-bottom:1.25rem;">
        {{ $this->form }}
    </div>

    <div style="display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:1rem; margin-bottom:1.25rem;" class="ds-plr-summary">
        @foreach([
            ['label' => 'Total Units',       'value' => number_format($r['totalUnits']),       'color' => '#3b82f6'],
            ['label' => 'Inventory (Cost)',  'value' => $fmt($r['totalValueCost']),            'color' => '#8b5cf6'],
            ['label' => 'Inventory (Retail)','value' => $fmt($r['totalValueRetail']),          'color' => '#10b981'],
            ['label' => 'Potential Profit',  'value' => $fmt($r['potentialProfit']),           'color' => '#f59e0b'],
        ] as $stat)
            <div class="ds-surface" style="border-radius:0.75rem; padding:1rem 1.25rem;">
                <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:0.375rem;">{{ $stat['label'] }}</div>
                <div style="font-size:1.5rem; font-weight:700; letter-spacing:-0.02em; color:{{ $stat['color'] }};">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>

    <div style="display:flex; gap:1rem; margin-bottom:1.25rem; flex-wrap:wrap;">
        <div class="ds-surface" style="flex:1; min-width:200px; padding:0.85rem 1.25rem; border-radius:0.75rem; border-left:4px solid #f59e0b !important;">
            <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; text-transform:uppercase;">Low Stock Alerts</div>
            <div style="font-size:1.4rem; font-weight:700; color:#f59e0b;">{{ $r['lowStockCount'] }}</div>
        </div>
        <div class="ds-surface" style="flex:1; min-width:200px; padding:0.85rem 1.25rem; border-radius:0.75rem; border-left:4px solid #ef4444 !important;">
            <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; text-transform:uppercase;">Out of Stock</div>
            <div style="font-size:1.4rem; font-weight:700; color:#ef4444;">{{ $r['outCount'] }}</div>
        </div>
        <div class="ds-surface" style="flex:1; min-width:200px; padding:0.85rem 1.25rem; border-radius:0.75rem; border-left:4px solid #10b981 !important;">
            <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; text-transform:uppercase;">Active Products</div>
            <div style="font-size:1.4rem; font-weight:700; color:#10b981;">{{ $r['productCount'] }}</div>
        </div>
    </div>

    <div class="ds-surface" style="border-radius:1rem; overflow:hidden;">
        <div class="ds-divider" style="padding:1rem 1.5rem; border-bottom:1px solid rgb(241 245 249);">
            <h3 class="ds-text-strong" style="margin:0; font-size:1rem; font-weight:700;">Products</h3>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr class="ds-text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em;">
                        <th style="text-align:left; padding:0.75rem 1.25rem;">Product</th>
                        <th style="text-align:left; padding:0.75rem 1.25rem;">SKU</th>
                        <th style="text-align:left; padding:0.75rem 1.25rem;">Brand</th>
                        <th style="text-align:right; padding:0.75rem 1.25rem;">Stock</th>
                        <th style="text-align:right; padding:0.75rem 1.25rem;">Min</th>
                        <th style="text-align:right; padding:0.75rem 1.25rem;">Cost</th>
                        <th style="text-align:right; padding:0.75rem 1.25rem;">Price</th>
                        <th style="text-align:right; padding:0.75rem 1.25rem;">Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($r['products'] as $p)
                        @php
                            $low = $p->in_stock && $p->stock_quantity <= $p->min_stock_quantity;
                            $out = !$p->in_stock || $p->stock_quantity <= 0;
                            $stockColor = $out ? '#ef4444' : ($low ? '#f59e0b' : '#10b981');
                        @endphp
                        <tr class="ds-divider" style="border-top:1px solid rgb(241 245 249);">
                            <td class="ds-text-strong" style="padding:0.7rem 1.25rem; font-size:0.88rem; font-weight:600;">
                                <a href="{{ url('/admin/products/' . $p->id . '/edit') }}" wire:navigate style="color:inherit; text-decoration:none;">{{ $p->name }}</a>
                            </td>
                            <td class="ds-text-muted" style="padding:0.7rem 1.25rem; font-size:0.82rem; font-family:monospace;">{{ $p->sku ?? '—' }}</td>
                            <td class="ds-text-muted" style="padding:0.7rem 1.25rem; font-size:0.85rem;">{{ $p->brand?->name ?? '—' }}</td>
                            <td style="padding:0.7rem 1.25rem; text-align:right; font-weight:700; color:{{ $stockColor }};">{{ (int) $p->stock_quantity }}</td>
                            <td class="ds-text-muted" style="padding:0.7rem 1.25rem; text-align:right;">{{ (int) $p->min_stock_quantity }}</td>
                            <td class="ds-text-muted" style="padding:0.7rem 1.25rem; text-align:right;">{{ $fmt((float) $p->cost_price) }}</td>
                            <td class="ds-text-strong" style="padding:0.7rem 1.25rem; text-align:right; font-weight:600;">{{ $fmt((float) $p->price) }}</td>
                            <td class="ds-text-strong" style="padding:0.7rem 1.25rem; text-align:right; font-weight:700;">{{ $fmt((float) $p->price * (int) $p->stock_quantity) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="ds-text-muted" style="padding:2rem; text-align:center;">No products match the filter.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        @media (max-width: 900px) {
            .ds-plr-summary { grid-template-columns:repeat(2, minmax(0, 1fr)) !important; }
        }
    </style>
</x-filament-panels::page>
