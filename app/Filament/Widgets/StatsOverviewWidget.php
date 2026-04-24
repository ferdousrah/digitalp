<?php

namespace App\Filament\Widgets;

use App\Models\ContactSubmission;
use App\Models\Order;
use App\Models\Product;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class StatsOverviewWidget extends Widget
{
    protected static string $view = 'filament.widgets.stats-overview-widget';
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $thisMonth    = Carbon::now()->startOfMonth();
        $lastMonth    = Carbon::now()->subMonth()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonth()->endOfMonth();

        // Total Orders — lifetime + change vs last month
        $thisMonthOrders = Order::where('created_at', '>=', $thisMonth)->count();
        $lastMonthOrders = Order::whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();
        $totalOrders     = Order::count();

        // New Customers — unique shipping_phone this month
        $newCustomersThisMonth = Order::where('created_at', '>=', $thisMonth)
            ->distinct('shipping_phone')->count('shipping_phone');
        $newCustomersLastMonth = Order::whereBetween('created_at', [$lastMonth, $lastMonthEnd])
            ->distinct('shipping_phone')->count('shipping_phone');

        // Pending orders — awaiting action
        $pendingOrders  = Order::where('status', 'pending')->count();
        $pendingLast    = Order::where('status', 'pending')
            ->whereBetween('created_at', [$lastMonth, $lastMonthEnd])->count();

        // Revenue — paid this month + last
        $revenueMonth     = (float) Order::where('payment_status', 'paid')
            ->where('created_at', '>=', $thisMonth)->sum('total');
        $revenueLastMonth = (float) Order::where('payment_status', 'paid')
            ->whereBetween('created_at', [$lastMonth, $lastMonthEnd])->sum('total');
        $revenueTotal     = (float) Order::where('payment_status', 'paid')->sum('total');

        $pct = fn ($now, $prev) => $prev > 0 ? round((($now - $prev) / $prev) * 100, 1) : null;

        return [
            'cards' => [
                [
                    'featured'    => true, // dark highlighted card
                    'label'       => 'Total Orders',
                    'value'       => number_format($totalOrders),
                    'trend'       => $pct($thisMonthOrders, $lastMonthOrders),
                    'lastLabel'   => 'Last month',
                    'lastValue'   => number_format($lastMonthOrders),
                    'iconBg'      => 'rgba(255,255,255,0.14)',
                    'iconColor'   => '#86efac',
                    'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z"/>',
                    'href'        => url('/admin/orders'),
                ],
                [
                    'label'       => 'New Customers',
                    'value'       => number_format($newCustomersThisMonth),
                    'trend'       => $pct($newCustomersThisMonth, $newCustomersLastMonth),
                    'lastLabel'   => 'Last month',
                    'lastValue'   => number_format($newCustomersLastMonth),
                    'iconBg'      => '#dbeafe',
                    'iconColor'   => '#3b82f6',
                    'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>',
                    'href'        => url('/admin/orders'),
                ],
                [
                    'label'       => 'Pending',
                    'value'       => number_format($pendingOrders),
                    'trend'       => $pct($pendingOrders, $pendingLast),
                    'trendInvert' => true, // for pending, fewer is better — invert color of arrow
                    'lastLabel'   => 'Last month',
                    'lastValue'   => number_format($pendingLast),
                    'iconBg'      => '#fef3c7',
                    'iconColor'   => '#f59e0b',
                    'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>',
                    'href'        => url('/admin/orders?tableFilters[status][value]=pending'),
                ],
                [
                    'label'       => 'Total Revenue',
                    'value'       => '৳' . number_format($revenueTotal, 2),
                    'trend'       => $pct($revenueMonth, $revenueLastMonth),
                    'lastLabel'   => 'Last month',
                    'lastValue'   => '৳' . number_format($revenueLastMonth, 0),
                    'iconBg'      => '#dcfce7',
                    'iconColor'   => '#10b981',
                    'icon'        => '<path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941"/>',
                    'href'        => url('/admin/orders?tableFilters[payment_status][value]=paid'),
                ],
            ],
        ];
    }
}
