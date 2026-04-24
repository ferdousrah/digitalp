<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersByStatusWidget extends ChartWidget
{
    protected static ?string $heading   = 'Orders by Status';
    protected static ?int    $sort      = 4;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $statuses = Order::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $colorMap = [
            'pending'    => '#f97316',
            'processing' => '#3b82f6',
            'shipped'    => '#8b5cf6',
            'delivered'  => '#10b981',
            'cancelled'  => '#ef4444',
            'completed'  => '#22c55e',
        ];

        $labels = array_map('ucfirst', array_keys($statuses));
        $data   = array_values($statuses);
        $colors = array_map(fn ($s) => $colorMap[$s] ?? '#9ca3af', array_keys($statuses));

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
            'plugins' => [
                'legend' => ['position' => 'bottom'],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(ctx){return ' '+ctx.label+': '+ctx.raw+' orders';}",
                    ],
                ],
            ],
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
