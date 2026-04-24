<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class RevenueChartWidget extends ChartWidget
{
    protected static ?string $heading    = 'Revenue — Last 30 Days';
    protected static ?string $maxHeight = '280px';
    protected static ?int    $sort       = 2;
    protected array|string|int $columnSpan = 'full';

    protected function getType(): string
    {
        return 'line';
    }

    protected function getData(): array
    {
        $days    = 30;
        $labels  = [];
        $revenue = [];
        $orders  = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date     = Carbon::today()->subDays($i);
            $labels[] = $date->format('M d');

            $revenue[] = (float) Order::where('payment_status', 'paid')
                ->whereDate('created_at', $date)
                ->sum('total');

            $orders[] = Order::whereDate('created_at', $date)->count();
        }

        return [
            'datasets' => [
                [
                    'label'                => 'Revenue (৳)',
                    'data'                 => $revenue,
                    'borderColor'          => '#f97316',
                    'backgroundColor'      => 'rgba(249,115,22,0.08)',
                    'fill'                 => true,
                    'tension'              => 0.4,
                    'pointBackgroundColor' => '#f97316',
                    'pointRadius'          => 3,
                    'yAxisID'              => 'y',
                ],
                [
                    'label'                => 'Orders',
                    'data'                 => $orders,
                    'borderColor'          => '#3b82f6',
                    'backgroundColor'      => 'rgba(59,130,246,0.05)',
                    'fill'                 => false,
                    'tension'              => 0.4,
                    'pointBackgroundColor' => '#3b82f6',
                    'pointRadius'          => 3,
                    'yAxisID'              => 'y1',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => true, 'position' => 'top'],
                'tooltip' => ['mode' => 'index', 'intersect' => false],
            ],
            'scales' => [
                'y'  => ['position' => 'left',  'grid' => ['color' => 'rgba(0,0,0,0.05)'], 'ticks' => ['callback' => "function(v){return '৳'+v.toLocaleString()}"]],
                'y1' => ['position' => 'right', 'grid' => ['drawOnChartArea' => false], 'title' => ['display' => true, 'text' => 'Orders']],
                'x'  => ['grid' => ['display' => false]],
            ],
            'interaction' => ['mode' => 'nearest', 'axis' => 'x', 'intersect' => false],
            'animation' => [
                'duration' => 1200,
                'easing'   => 'easeInOutQuart',
            ],
            'animations' => [
                'x' => [
                    'type'     => 'number',
                    'easing'   => 'easeInOutQuart',
                    'duration' => 1200,
                    'from'     => 0,
                ],
                'y' => [
                    'type'     => 'number',
                    'easing'   => 'easeInOutQuart',
                    'duration' => 1200,
                    'from'     => 0,
                ],
            ],
        ];
    }
}
