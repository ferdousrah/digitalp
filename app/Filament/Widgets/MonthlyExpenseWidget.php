<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class MonthlyExpenseWidget extends ChartWidget
{
    protected static ?string $heading = 'Revenue vs Expenses — Last 12 Months';
    protected static ?int $sort = 3;
    protected array|string|int $columnSpan = ['default' => 'full', 'md' => 1, 'xl' => 2];
    protected static ?string $maxHeight = '320px';

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $labels   = [];
        $revenue  = [];
        $expenses = [];

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');

            $revenue[] = (float) Order::where('payment_status', 'paid')
                ->whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->sum('total');

            $expenses[] = (float) Expense::whereYear('expense_date', $month->year)
                ->whereMonth('expense_date', $month->month)
                ->sum('amount');
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Revenue (৳)',
                    'data'            => $revenue,
                    'backgroundColor' => 'rgba(16,185,129,0.9)',
                    'borderColor'     => '#059669',
                    'borderWidth'     => 0,
                    'borderRadius'    => 10,
                    'borderSkipped'   => false,
                    'hoverBackgroundColor' => '#34d399',
                ],
                [
                    'label'           => 'Expenses (৳)',
                    'data'            => $expenses,
                    'backgroundColor' => 'rgba(239,68,68,0.9)',
                    'borderColor'     => '#dc2626',
                    'borderWidth'     => 0,
                    'borderRadius'    => 10,
                    'borderSkipped'   => false,
                    'hoverBackgroundColor' => '#f87171',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend'  => ['display' => true, 'position' => 'top', 'labels' => ['usePointStyle' => true, 'boxWidth' => 10]],
                'tooltip' => [
                    'mode' => 'index',
                    'intersect' => false,
                    'callbacks' => [
                        'label' => "function(ctx){return ' '+ctx.dataset.label+': ৳'+ctx.raw.toLocaleString();}",
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'grid' => ['color' => 'rgba(0,0,0,0.04)'],
                    'ticks' => ['callback' => "function(v){return '৳'+v.toLocaleString()}"],
                ],
                'x' => ['grid' => ['display' => false]],
            ],
            'barPercentage'      => 0.8,
            'categoryPercentage' => 0.8,
            'animation' => ['duration' => 1000, 'easing' => 'easeOutQuart'],
        ];
    }
}
