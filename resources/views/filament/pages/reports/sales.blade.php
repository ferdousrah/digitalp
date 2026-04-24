<x-filament-panels::page>
    @php
        $r = $this->getReport();
        $fmt = fn ($v) => '৳' . number_format($v, 2);
    @endphp

    <div class="ds-surface" style="padding:1.25rem 1.5rem; border-radius:1rem; margin-bottom:1.25rem;">
        {{ $this->form }}
    </div>

    {{-- Summary cards --}}
    <div style="display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:1rem; margin-bottom:1.25rem;" class="ds-plr-summary">
        @foreach([
            ['label' => 'Total Orders',   'value' => number_format($r['totalOrders']),        'color' => '#3b82f6'],
            ['label' => 'Paid Revenue',   'value' => $fmt($r['totalRevenue']),                'color' => '#10b981'],
            ['label' => 'Pending Orders', 'value' => number_format($r['pendingCount']),       'color' => '#f59e0b'],
            ['label' => 'Avg Order',      'value' => $fmt($r['avgOrder']),                    'color' => '#8b5cf6'],
        ] as $stat)
            <div class="ds-surface" style="border-radius:0.75rem; padding:1rem 1.25rem;">
                <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:0.375rem;">{{ $stat['label'] }}</div>
                <div style="font-size:1.5rem; font-weight:700; letter-spacing:-0.02em; color:{{ $stat['color'] }};">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>

    <div class="ds-surface" style="border-radius:1rem; overflow:hidden;">
        <div class="ds-divider" style="padding:1rem 1.5rem; border-bottom:1px solid rgb(241 245 249); display:flex; justify-content:space-between; align-items:center;">
            <h3 class="ds-text-strong" style="margin:0; font-size:1rem; font-weight:700;">Orders (first 500 in range)</h3>
            <span class="ds-text-muted" style="font-size:0.78rem;">{{ $r['orders']->count() }} shown</span>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr class="ds-text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em;">
                        <th style="text-align:left; padding:0.75rem 1.25rem;">Date</th>
                        <th style="text-align:left; padding:0.75rem 1.25rem;">Order #</th>
                        <th style="text-align:left; padding:0.75rem 1.25rem;">Customer</th>
                        <th style="text-align:left; padding:0.75rem 1.25rem;">Status</th>
                        <th style="text-align:left; padding:0.75rem 1.25rem;">Payment</th>
                        <th style="text-align:right; padding:0.75rem 1.25rem;">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($r['orders'] as $o)
                        <tr class="ds-divider" style="border-top:1px solid rgb(241 245 249);">
                            <td class="ds-text-muted" style="padding:0.7rem 1.25rem; font-size:0.82rem;">{{ $o->created_at->format('M d, Y') }}</td>
                            <td class="ds-text-strong" style="padding:0.7rem 1.25rem; font-weight:600; font-size:0.88rem;">
                                <a href="{{ url('/admin/orders/' . $o->id . '/edit') }}" wire:navigate style="color:inherit; text-decoration:none;">#{{ $o->order_number }}</a>
                            </td>
                            <td class="ds-text-strong" style="padding:0.7rem 1.25rem; font-size:0.88rem;">{{ $o->shipping_name ?? '—' }}</td>
                            <td style="padding:0.7rem 1.25rem;"><span style="display:inline-block; padding:2px 10px; border-radius:99px; font-size:0.72rem; font-weight:600; background:#e0f2fe; color:#0369a1;">{{ ucfirst($o->status) }}</span></td>
                            <td style="padding:0.7rem 1.25rem;"><span style="display:inline-block; padding:2px 10px; border-radius:99px; font-size:0.72rem; font-weight:600; background:{{ $o->payment_status === 'paid' ? '#dcfce7' : '#fef3c7' }}; color:{{ $o->payment_status === 'paid' ? '#15803d' : '#92400e' }};">{{ ucfirst($o->payment_status) }}</span></td>
                            <td style="padding:0.7rem 1.25rem; text-align:right; font-weight:700;">{{ $fmt($o->total) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="ds-text-muted" style="padding:2rem; text-align:center;">No orders in the selected range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <style>
        @media (max-width: 900px) {
            .ds-plr-summary { grid-template-columns:repeat(2, minmax(0, 1fr)) !important; }
        }
        @media (max-width: 500px) {
            .ds-plr-summary { grid-template-columns:1fr !important; }
        }
    </style>
</x-filament-panels::page>
