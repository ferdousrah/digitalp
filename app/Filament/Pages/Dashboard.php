<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon  = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Sales Dashboard';
    protected static ?string $title           = 'Sales Analytics';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\StatsOverviewWidget::class,
            \App\Filament\Widgets\RevenueChartWidget::class,
            \App\Filament\Widgets\MonthlySalesChartWidget::class,
            \App\Filament\Widgets\OrdersByStatusWidget::class,
            \App\Filament\Widgets\OrdersByPaymentWidget::class,
            \App\Filament\Widgets\RecentOrdersWidget::class,
            \App\Filament\Widgets\TopProductsWidget::class,
            \App\Filament\Widgets\LowStockWidget::class,
            \App\Filament\Widgets\SlowSellersWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return [
            'sm'  => 1,
            'md'  => 2,
            'xl'  => 4,
        ];
    }
}
