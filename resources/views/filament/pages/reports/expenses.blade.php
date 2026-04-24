<x-filament-panels::page>
    @php
        $r = $this->getReport();
        $fmt = fn ($v) => '৳' . number_format($v, 2);
    @endphp

    <div class="ds-surface" style="padding:1.25rem 1.5rem; border-radius:1rem; margin-bottom:1.25rem;">
        {{ $this->form }}
    </div>

    {{-- Summary --}}
    <div style="display:grid; grid-template-columns:repeat(3, minmax(0, 1fr)); gap:1rem; margin-bottom:1.25rem;" class="ds-plr-summary">
        @foreach([
            ['label' => 'Total Expenses',    'value' => $fmt($r['total']),          'color' => '#ef4444'],
            ['label' => '# Expense Entries', 'value' => number_format($r['count']), 'color' => '#3b82f6'],
            ['label' => 'Avg Expense',       'value' => $fmt($r['avg']),            'color' => '#8b5cf6'],
        ] as $stat)
            <div class="ds-surface" style="border-radius:0.75rem; padding:1rem 1.25rem;">
                <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:0.375rem;">{{ $stat['label'] }}</div>
                <div style="font-size:1.5rem; font-weight:700; letter-spacing:-0.02em; color:{{ $stat['color'] }};">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Category aggregate --}}
    <div class="ds-surface" style="border-radius:1rem; overflow:hidden; margin-bottom:1.25rem;">
        <div class="ds-divider" style="padding:1rem 1.5rem; border-bottom:1px solid rgb(241 245 249);">
            <h3 class="ds-text-strong" style="margin:0; font-size:1rem; font-weight:700;">By Category</h3>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr class="ds-text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em;">
                        <th style="text-align:left; padding:0.75rem 1.25rem;">Category</th>
                        <th style="text-align:right; padding:0.75rem 1.25rem;">Entries</th>
                        <th style="text-align:right; padding:0.75rem 1.25rem;">Total</th>
                        <th style="text-align:right; padding:0.75rem 1.25rem;">% of total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($r['byCategory'] as $c)
                        <tr class="ds-divider" style="border-top:1px solid rgb(241 245 249);">
                            <td class="ds-text-strong" style="padding:0.75rem 1.25rem; font-weight:600; font-size:0.88rem;">
                                <span style="display:inline-flex; align-items:center; gap:0.5rem;">
                                    <span style="width:8px; height:8px; border-radius:50%; background:{{ $c->color ?? '#64748b' }};"></span>
                                    {{ $c->name ?? 'Uncategorised' }}
                                </span>
                            </td>
                            <td class="ds-text-muted" style="padding:0.75rem 1.25rem; text-align:right;">{{ number_format($c->count) }}</td>
                            <td style="padding:0.75rem 1.25rem; text-align:right; color:#ef4444; font-weight:700;">{{ $fmt((float) $c->total) }}</td>
                            <td class="ds-text-muted" style="padding:0.75rem 1.25rem; text-align:right; font-weight:500;">{{ $r['total'] > 0 ? round(((float) $c->total / $r['total']) * 100, 1) : 0 }}%</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="ds-text-muted" style="padding:2rem; text-align:center;">No expenses in range.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Detail rows --}}
    <div class="ds-surface" style="border-radius:1rem; overflow:hidden;">
        <div class="ds-divider" style="padding:1rem 1.5rem; border-bottom:1px solid rgb(241 245 249);">
            <h3 class="ds-text-strong" style="margin:0; font-size:1rem; font-weight:700;">Detailed Expenses</h3>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr class="ds-text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em;">
                        <th style="text-align:left; padding:0.75rem 1.25rem;">Date</th>
                        <th style="text-align:left; padding:0.75rem 1.25rem;">Title</th>
                        <th style="text-align:left; padding:0.75rem 1.25rem;">Category</th>
                        <th style="text-align:left; padding:0.75rem 1.25rem;">Paid To</th>
                        <th style="text-align:right; padding:0.75rem 1.25rem;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($r['expenses'] as $e)
                        <tr class="ds-divider" style="border-top:1px solid rgb(241 245 249);">
                            <td class="ds-text-muted" style="padding:0.7rem 1.25rem; font-size:0.82rem;">{{ $e->expense_date->format('M d, Y') }}</td>
                            <td class="ds-text-strong" style="padding:0.7rem 1.25rem; font-size:0.88rem; font-weight:600;">{{ $e->title }}</td>
                            <td class="ds-text-strong" style="padding:0.7rem 1.25rem; font-size:0.85rem;">
                                <span style="display:inline-flex; align-items:center; gap:0.4rem;">
                                    <span style="width:7px; height:7px; border-radius:50%; background:{{ $e->category?->color ?? '#64748b' }};"></span>
                                    {{ $e->category?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="ds-text-muted" style="padding:0.7rem 1.25rem; font-size:0.85rem;">{{ $e->paid_to ?? '—' }}</td>
                            <td style="padding:0.7rem 1.25rem; text-align:right; color:#ef4444; font-weight:700;">{{ $fmt($e->amount) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
