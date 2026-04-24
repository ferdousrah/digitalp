<x-filament-panels::page>
    @php
        $report = $this->getMonthlyBreakdown();
        $fmt = fn ($v) => '৳' . number_format($v, 2);
    @endphp

    {{-- Filter form --}}
    <div class="ds-surface" style="padding:1.25rem 1.5rem; border-radius:1rem; margin-bottom:1.25rem;">
        {{ $this->form }}
    </div>

    {{-- Summary cards --}}
    <div style="display:grid; grid-template-columns:repeat(4, minmax(0, 1fr)); gap:1rem; margin-bottom:1.25rem;" class="ds-plr-summary">
        @foreach([
            ['label' => 'Total Revenue', 'value' => $fmt($report['totalRevenue']), 'color' => '#10b981'],
            ['label' => 'Total Expenses', 'value' => $fmt($report['totalExpense']), 'color' => '#ef4444'],
            ['label' => 'Net Profit', 'value' => $fmt($report['totalProfit']), 'color' => $report['totalProfit'] >= 0 ? '#10b981' : '#ef4444'],
            ['label' => 'Margin', 'value' => $report['totalMargin'] !== null ? $report['totalMargin'] . '%' : '—', 'color' => '#3b82f6'],
        ] as $stat)
            <div class="ds-surface" style="border-radius:0.75rem; padding:1rem 1.25rem;">
                <div class="ds-text-muted" style="font-size:0.72rem; font-weight:600; letter-spacing:0.04em; text-transform:uppercase; margin-bottom:0.375rem;">{{ $stat['label'] }}</div>
                <div style="font-size:1.5rem; font-weight:700; letter-spacing:-0.02em; color:{{ $stat['color'] }};">{{ $stat['value'] }}</div>
            </div>
        @endforeach
    </div>

    {{-- Monthly breakdown table --}}
    <div class="ds-surface" style="border-radius:1rem; overflow:hidden;">
        <div style="padding:1rem 1.5rem; border-bottom:1px solid rgb(241 245 249);" class="ds-divider">
            <h3 class="ds-text-strong" style="margin:0; font-size:1rem; font-weight:700;">Monthly Breakdown</h3>
        </div>
        <div style="overflow-x:auto;">
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr class="ds-text-muted" style="font-size:0.72rem; text-transform:uppercase; letter-spacing:0.04em;">
                        <th style="text-align:left; padding:0.75rem 1.5rem;">Period</th>
                        <th style="text-align:right; padding:0.75rem 1.5rem;">Revenue</th>
                        <th style="text-align:right; padding:0.75rem 1.5rem;">Expenses</th>
                        <th style="text-align:right; padding:0.75rem 1.5rem;">Net Profit</th>
                        <th style="text-align:right; padding:0.75rem 1.5rem;">Margin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($report['rows'] as $row)
                        <tr class="ds-divider" style="border-top:1px solid rgb(241 245 249);">
                            <td class="ds-text-strong" style="padding:0.85rem 1.5rem; font-weight:600; font-size:0.88rem;">{{ $row['label'] }}</td>
                            <td style="padding:0.85rem 1.5rem; text-align:right; color:#10b981; font-weight:600;">{{ $fmt($row['revenue']) }}</td>
                            <td style="padding:0.85rem 1.5rem; text-align:right; color:#ef4444; font-weight:600;">{{ $fmt($row['expense']) }}</td>
                            <td style="padding:0.85rem 1.5rem; text-align:right; font-weight:700; color:{{ $row['profit'] >= 0 ? '#059669' : '#dc2626' }};">{{ $fmt($row['profit']) }}</td>
                            <td class="ds-text-muted" style="padding:0.85rem 1.5rem; text-align:right; font-weight:500;">{{ $row['margin'] !== null ? $row['margin'] . '%' : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="ds-surface-muted ds-text-strong" style="font-weight:700;">
                        <td style="padding:1rem 1.5rem;">TOTAL</td>
                        <td style="padding:1rem 1.5rem; text-align:right; color:#10b981;">{{ $fmt($report['totalRevenue']) }}</td>
                        <td style="padding:1rem 1.5rem; text-align:right; color:#ef4444;">{{ $fmt($report['totalExpense']) }}</td>
                        <td style="padding:1rem 1.5rem; text-align:right; color:{{ $report['totalProfit'] >= 0 ? '#059669' : '#dc2626' }};">{{ $fmt($report['totalProfit']) }}</td>
                        <td style="padding:1rem 1.5rem; text-align:right;">{{ $report['totalMargin'] !== null ? $report['totalMargin'] . '%' : '—' }}</td>
                    </tr>
                </tfoot>
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
