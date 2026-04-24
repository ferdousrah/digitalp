<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today         = Carbon::today();
        $thisMonth     = Carbon::now()->startOfMonth();
        $lastMonth     = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd  = Carbon::now()->subMonth()->endOfMonth();

        $totalRevenue      = Order::where('payment_status', 'paid')->sum('total');
        $monthRevenue      = Order::where('payment_status', 'paid')->where('created_at', '>=', $thisMonth)->sum('total');
        $lastMonthRevenue  = Order::where('payment_status', 'paid')->whereBetween('created_at', [$lastMonth, $lastMonthEnd])->sum('total');
        $revenueChange     = $lastMonthRevenue > 0 ? round((($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100, 1) : 0;

        $totalOrders   = Order::count();
        $todayOrders   = Order::whereDate('created_at', $today)->count();
        $pendingOrders = Order::where('status', 'pending')->count();
        $paidOrders    = Order::where('payment_status', 'paid')->count();

        // 7-day sparkline for revenue
        $revenueTrend = collect(range(6, 0))->map(
            fn ($d) => (float) Order::where('payment_status', 'paid')
                ->whereDate('created_at', Carbon::today()->subDays($d))
                ->sum('total')
        )->toArray();

        // 7-day sparkline for orders
        $orderTrend = collect(range(6, 0))->map(
            fn ($d) => Order::whereDate('created_at', Carbon::today()->subDays($d))->count()
        )->toArray();

        return [
            Stat::make('Total Revenue', '৳' . number_format($totalRevenue, 2))
                ->description('৳' . number_format($monthRevenue, 2) . ' this month' . ($revenueChange != 0 ? ' (' . ($revenueChange > 0 ? '+' : '') . $revenueChange . '%)' : ''))
                ->descriptionIcon($revenueChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($revenueChange >= 0 ? 'success' : 'danger')
                ->chart($revenueTrend),

            Stat::make('Total Orders', number_format($totalOrders))
                ->description($todayOrders . ' new today')
                ->descriptionIcon('heroicon-m-shopping-bag')
                ->color('info')
                ->chart($orderTrend),

            Stat::make('Pending Orders', number_format($pendingOrders))
                ->description('Awaiting processing')
                ->descriptionIcon('heroicon-m-clock')
                ->color($pendingOrders > 10 ? 'warning' : 'gray'),

            Stat::make('Paid Orders', number_format($paidOrders))
                ->description(number_format($totalOrders > 0 ? ($paidOrders / $totalOrders) * 100 : 0, 1) . '% conversion rate')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
