<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersByPaymentWidget extends ChartWidget
{
    protected static ?string $heading   = 'Payment Methods';
    protected static ?int    $sort      = 5;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $methods = Order::selectRaw('payment_method, COUNT(*) as count')
            ->groupBy('payment_method')
            ->pluck('count', 'payment_method')
            ->toArray();

        $labelMap = [
            'cod'    => 'Cash on Delivery',
            'bkash'  => 'bKash',
            'online' => 'Online (SSLCommerz)',
        ];

        $colorMap = [
            'cod'    => '#10b981',
            'bkash'  => '#e20074',
            'online' => '#0c713a',
        ];

        $labels = array_map(fn ($m) => $labelMap[$m] ?? ucfirst($m), array_keys($methods));
        $data   = array_values($methods);
        $colors = array_map(fn ($m) => $colorMap[$m] ?? '#9ca3af', array_keys($methods));

        return [
            'datasets' => [[
                'data'            => $data,
                'backgroundColor' => $colors,
                'hoverOffset'     => 6,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['position' => 'bottom']],
            'cutout' => '65%',
            'animation' => [
                'animateRotate' => true,
                'animateScale'  => true,
                'duration'      => 1000,
                'easing'        => 'easeOutQuart',
            ],
        ];
    }
}
