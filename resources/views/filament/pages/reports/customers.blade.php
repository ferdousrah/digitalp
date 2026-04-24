<x-filament-panels::page>
    @php
        $r = $this->getReport();
        $fmt = fn ($v) => '৳' . number_format($v, 2);
    @endphp

    <div class="ds-surface" style="padding:1.25rem 1.5rem; border-radius:1rem; margin-bottom:1.25rem;">
        {{ $this->form }}
    </div>

    <div style="display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:1rem; margin-bottom:1.25rem;" class="ds-plr-summary">
        <div class="ds-surface" style="border-radius:0.75rem; padding:1rem 1.25rem;">
            <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:0.375rem;">Unique Customers</div>
            <div style="font-size:1.5rem; font-weight:700; color:#3b82f6;">{{ number_format($r['count']) }}</div>
        </div>
        <div class="ds-surface" style="border-radius:0.75rem; padding:1rem 1.25rem;">
            <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:0.375rem;">Total Customer Spend</div>
            <div style="font-size:1.5rem; font-weight:700; color:#10b981;">{{ $fmt($r['totalSpend']) }}</div>
        </div>
        <div class="ds-surface" style="border-radius:0.75rem; padding:1rem 1.25rem;">
            <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:0.375rem;">Top Customer</div>
            <div class="ds-text-strong" style="font-size:1rem; font-weight:700;">{{ $r['topSpender']?->name ?? '—' }}</div>
            @if($r['topSpender'])
                <div class="ds-text-muted" style="font-size:0.82rem; margin-top:0.125rem;">{{ $fmt((float) $r['topSpender']->total_spent) }} — {{ $r['topSpender']->total_orders }} orders</div>
            @endif
        </div>
    </div>

    <div class="ds-surface" style="border-radius:1rem; overflow:hidden;">
        <div class="ds-divider" style="padding:1rem 1.5rem; border-bottom:1px solid rgb(241 245 249);">
            <h3 class="ds-text-strong" style="margin:0; font-size:1rem; font-weight:700;">Top Customers (by spend)</h3>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr class="ds-text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em;">
                        <th style="text-align:left; padding:0.75rem 1.25rem;">#</th>
                        <th style="text-align:left; padding:0.75rem 1.25rem;">Customer</th>
                        <th style="text-align:left; padding:0.75rem 1.25rem;">Phone</th>
                        <th style="text-align:right; padding:0.75rem 1.25rem;">Orders</th>
                        <th style="text-align:right; padding:0.75rem 1.25rem;">Total Spent</th>
                        <th style="text-align:right; padding:0.75rem 1.25rem;">Last Order</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($r['customers'] as $i => $c)
                        <tr class="ds-divider" style="border-top:1px solid rgb(241 245 249);">
                            <td class="ds-text-muted" style="padding:0.7rem 1.25rem; font-size:0.82rem; font-weight:700;">{{ $i + 1 }}</td>
                            <td class="ds-text-strong" style="padding:0.7rem 1.25rem; font-size:0.88rem; font-weight:600;">{{ $c->name ?? '—' }}</td>
                            <td class="ds-text-muted" style="padding:0.7rem 1.25rem; font-size:0.85rem; font-family:monospace;">{{ $c->phone }}</td>
                            <td class="ds-text-strong" style="padding:0.7rem 1.25rem; text-align:right; font-weight:700;">{{ $c->total_orders }}</td>
                            <td style="padding:0.7rem 1.25rem; text-align:right; font-weight:700; color:#10b981;">{{ $fmt((float) $c->total_spent) }}</td>
                            <td class="ds-text-muted" style="padding:0.7rem 1.25rem; text-align:right; font-size:0.82rem;">{{ \Carbon\Carbon::parse($c->last_order)->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="ds-text-muted" style="padding:2rem; text-align:center;">No customers in the selected period.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
