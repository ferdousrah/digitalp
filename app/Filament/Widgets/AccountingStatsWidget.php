<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Order;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class AccountingStatsWidget extends Widget
{
    // Reuse the polished stats-overview template — it just needs $cards
    protected static string $view = 'filament.widgets.stats-overview-widget';
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $thisMonth    = Carbon::now()->startOfMonth();
        $lastMonth    = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Revenue (paid orders)
        $revenueTotal     = (float) Order::where('payment_status', 'paid')->sum('total');
        $revenueThisMonth = (float) Order::where('payment_status', 'paid')->where('created_at', '>=', $thisMonth)->sum('total');
        $revenueLastMonth = (float) Order::where('payment_status', 'paid')->whereBetween('created_at', [$lastMonth, $lastMonthEnd])->sum('total');

        // Expenses
        $expenseTotal     = (float) Expense::sum('amount');
        $expenseThisMonth = (float) Expense::where('expense_date', '>=', $thisMonth)->sum('amount');
        $expenseLastMonth = (float) Expense::whereBetween('expense_date', [$lastMonth, $lastMonthEnd])->sum('amount');

        // Profit
        $profitTotal     = $revenueTotal - $expenseTotal;
        $profitThisMonth = $revenueThisMonth - $expenseThisMonth;
        $profitLastMonth = $revenueLastMonth - $expenseLastMonth;

        $pct = fn ($now, $prev) => $prev != 0 ? round((($now - $prev) / abs($prev)) * 100, 1) : ($now != 0 ? 100 : null);

        // Last-month margin for the Margin card's footer comparison
        $marginLastMonth = $revenueLastMonth > 0
            ? round(($profitLastMonth / $revenueLastMonth) * 100, 1) . '%'
            : '—';

        return [
            'cards' => [
                [
                    'featured'   => true,
                    'label'      => 'Net Profit',
                    'value'      => '৳' . number_format($profitTotal, 2),
                    'trend'      => $pct($profitThisMonth, $profitLastMonth),
                    'lastLabel'  => 'Last month',
                    'lastValue'  => '৳' . number_format($profitLastMonth, 0),
                    'iconBg'     => 'rgba(255,255,255,0.14)',
                    'iconColor'  => '#86efac',
                    'icon'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/>',
                    'href'       => url('/admin/expenses'),
                ],
                [
                    'label'      => 'Revenue',
                    'value'      => '৳' . number_format($revenueTotal, 2),
                    'trend'      => $pct($revenueThisMonth, $revenueLastMonth),
                    'lastLabel'  => 'Last month',
                    'lastValue'  => '৳' . number_format($revenueLastMonth, 0),
                    'iconBg'     => '#dcfce7',
                    'iconColor'  => '#10b981',
                    'icon'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Z"/>',
                    'href'       => url('/admin/orders?tableFilters[payment_status][value]=paid'),
                ],
                [
                    'label'        => 'Expenses',
                    'value'        => '৳' . number_format($expenseTotal, 2),
                    'trend'        => $pct($expenseThisMonth, $expenseLastMonth),
                    'trendInvert'  => true, // higher expenses = bad (red)
                    'lastLabel'    => 'Last month',
                    'lastValue'    => '৳' . number_format($expenseLastMonth, 0),
                    'iconBg'       => '#fee2e2',
                    'iconColor'    => '#ef4444',
                    'icon'         => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 0 5.814-5.518l2.74-1.22m0 0L12.74 10.5M21.75 6.75V12.5"/>',
                    'href'         => url('/admin/expenses'),
                ],
                [
                    'label'      => 'Margin',
                    'value'      => $revenueTotal > 0
                        ? number_format(($profitTotal / $revenueTotal) * 100, 1) . '%'
                        : '0%',
                    'trend'      => null,
                    'lastLabel'  => 'Last month margin',
                    'lastValue'  => $marginLastMonth,
                    'iconBg'     => '#dbeafe',
                    'iconColor'  => '#3b82f6',
                    'icon'       => '<path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z"/>',
                    'href'       => url('/admin/accounting'),
                ],
            ],
        ];
    }
}
