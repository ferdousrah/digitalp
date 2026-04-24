<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class ExpenseByCategoryWidget extends ChartWidget
{
    protected static ?string $heading = 'Expenses by Category';
    protected static ?int $sort = 2;
    protected array|string|int $columnSpan = ['default' => 'full', 'md' => 1, 'xl' => 2];
    protected static ?string $maxHeight = '320px';

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getData(): array
    {
        $rows = DB::table('expenses')
            ->join('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
            ->selectRaw('expense_categories.name as name, expense_categories.color as color, SUM(expenses.amount) as total')
            ->groupBy('expense_categories.id', 'expense_categories.name', 'expense_categories.color')
            ->orderByDesc('total')
            ->get();

        return [
            'datasets' => [[
                'data'             => $rows->pluck('total')->map(fn ($v) => (float) $v)->toArray(),
                'backgroundColor'  => $rows->pluck('color')->toArray(),
                'borderColor'      => '#ffffff',
                'borderWidth'      => 4,
                'hoverOffset'      => 14,
                'hoverBorderWidth' => 6,
                'spacing'          => 2,
            ]],
            'labels' => $rows->pluck('name')->toArray(),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'position' => 'bottom',
                    'labels'   => ['usePointStyle' => true, 'padding' => 16, 'boxWidth' => 10],
                ],
                'tooltip' => [
                    'callbacks' => [
                        'label' => "function(ctx){return ' '+ctx.label+': ৳'+ctx.raw.toLocaleString();}",
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
