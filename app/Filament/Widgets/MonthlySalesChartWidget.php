<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Filament\Forms\Components\Select;
use Illuminate\Support\Carbon;

class MonthlySalesChartWidget extends ChartWidget
{
    protected static ?string  $heading    = 'Monthly Sales & Revenue';
    protected static ?int     $sort       = 3;
    protected array|string|int $columnSpan = 'full';
    protected static ?string  $maxHeight  = '280px';

    public ?string $filter = null;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFilters(): ?array
    {
        $currentYear = Carbon::now()->year;
        $firstYear   = (int) (Order::min('created_at') ? Carbon::parse(Order::min('created_at'))->year : $currentYear);

        $years = [];
        for ($y = $currentYear; $y >= $firstYear; $y--) {
            $years[(string) $y] = (string) $y;
        }

        return $years;
    }

    protected function getData(): array
    {
        $year = $this->filter ?? Carbon::now()->year;

        $months  = [];
        $revenue = [];
        $orders  = [];

        for ($m = 1; $m <= 12; $m++) {
            $months[] = Carbon::createFromDate($year, $m, 1)->format('M');

            $revenue[] = (float) Order::where('payment_status', 'paid')
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->sum('total');

            $orders[] = Order::whereYear('created_at', $year)
                ->whereMonth('created_at', $m)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Revenue (৳)',
                    'data'            => $revenue,
                    // Layered "3D" effect: top color = orange, gradient simulated via
                    // lighter top + darker bottom via a client-side plugin (see options)
                    'backgroundColor' => 'rgba(249,115,22,0.9)',
                    'borderColor'     => '#ea580c',
                    'borderWidth'     => 0,
                    'borderRadius'    => 10,
                    'borderSkipped'   => false,
                    'yAxisID'         => 'y',
                    'hoverBackgroundColor' => '#fb923c',
                ],
                [
                    'label'           => 'Orders',
                    'data'            => $orders,
                    'backgroundColor' => 'rgba(59,130,246,0.85)',
                    'borderColor'     => '#2563eb',
                    'borderWidth'     => 0,
                    'borderRadius'    => 10,
                    'borderSkipped'   => false,
                    'yAxisID'         => 'y1',
                    'hoverBackgroundColor' => '#60a5fa',
                ],
            ],
            'labels' => $months,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend'  => ['display' => true, 'position' => 'top'],
                'tooltip' => ['mode' => 'index', 'intersect' => false],
            ],
            'scales' => [
                'y' => [
                    'position'  => 'left',
                    'beginAtZero' => true,
                    'grid'      => ['color' => 'rgba(0,0,0,0.05)'],
                    'title'     => ['display' => true, 'text' => 'Revenue (৳)'],
                ],
                'y1' => [
                    'position'       => 'right',
                    'beginAtZero'    => true,
                    'grid'           => ['drawOnChartArea' => false],
                    'title'          => ['display' => true, 'text' => 'Orders'],
                ],
                'x' => [
                    'grid' => ['display' => false],
                ],
            ],
            'interaction' => ['mode' => 'nearest', 'axis' => 'x', 'intersect' => false],
            'barPercentage'      => 0.85,
            'categoryPercentage' => 0.85,
            'animation' => [
                'duration' => 1000,
                'easing'   => 'easeOutQuart',
                'delay'    => 0,
            ],
            'animations' => [
                'y' => [
                    'from'     => 0,
                    'duration' => 800,
                    'easing'   => 'easeOutCubic',
                ],
                'x' => [
                    'duration' => 0,
                ],
            ],
            'transitions' => [
                'active' => [
                    'animation' => ['duration' => 300],
                ],
            ],
        ];
    }
}
