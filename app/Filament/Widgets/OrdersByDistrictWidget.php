<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrdersByDistrictWidget extends ChartWidget
{
    protected static ?string $heading = 'Orders by District';
    protected static ?int $sort = 6;

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $rows = Order::query()
            ->whereNotNull('shipping_district')
            ->where('shipping_district', '!=', '')
            ->selectRaw('shipping_district, COUNT(*) as count')
            ->groupBy('shipping_district')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Distinct palette — 10 colors so top-10 districts are all readable
        $palette = [
            '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
            '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1',
        ];

        $labels = $rows->pluck('shipping_district')->toArray();
        $data   = $rows->pluck('count')->map(fn ($v) => (int) $v)->toArray();
        $colors = array_slice($palette, 0, max(1, count($labels)));

        return [
            'datasets' => [[
                'data'             => $data,
                'backgroundColor'  => $colors,
                'borderColor'      => '#ffffff',
                'borderWidth'      => 4,
                'hoverOffset'      => 14,
                'hoverBorderWidth' => 6,
                'spacing'          => 2,
            ]],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels'   => ['usePointStyle' => true, 'padding' => 14, 'boxWidth' => 10],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(ctx){return ' '+ctx.label+': '+ctx.raw+' orders';}",
                    ],
                ],
            ],
            'cutout' => '68%',
            'animation' => [
                'animateRotate' => true,
                'animateScale'  => true,
                'duration'      => 1400,
                'easing'        => 'easeOutElastic',
            ],
        ];
    }
}
