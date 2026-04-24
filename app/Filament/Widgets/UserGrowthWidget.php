<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

class UserGrowthWidget extends Widget
{
    protected static string $view = 'filament.widgets.user-growth-widget';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = ['default' => 'full', 'md' => 1, 'xl' => 2];

    public ?string $compareTo = 'year';

    public function setCompare(string $value): void
    {
        $this->compareTo = $value;
    }

    protected function getViewData(): array
    {
        $now      = Carbon::now();
        $thisStart = match ($this->compareTo) {
            'year'  => $now->copy()->startOfYear(),
            'month' => $now->copy()->startOfMonth(),
            'week'  => $now->copy()->startOfWeek(),
            default => $now->copy()->startOfYear(),
        };
        $prevStart = match ($this->compareTo) {
            'year'  => $now->copy()->subYear()->startOfYear(),
            'month' => $now->copy()->subMonth()->startOfMonth(),
            'week'  => $now->copy()->subWeek()->startOfWeek(),
            default => $now->copy()->subYear()->startOfYear(),
        };
        $prevEnd = match ($this->compareTo) {
            'year'  => $now->copy()->subYear()->endOfYear(),
            'month' => $now->copy()->subMonth()->endOfMonth(),
            'week'  => $now->copy()->subWeek()->endOfWeek(),
            default => $now->copy()->subYear()->endOfYear(),
        };

        $current  = Order::where('created_at', '>=', $thisStart)
            ->distinct('shipping_phone')->count('shipping_phone');
        $previous = Order::whereBetween('created_at', [$prevStart, $prevEnd])
            ->distinct('shipping_phone')->count('shipping_phone');

        $pct = $previous > 0
            ? round((($current - $previous) / $previous) * 100, 1)
            : ($current > 0 ? 100 : 0);

        // Gauge fill: clamp to 0..100 based on growth. If growing, fill proportionally
        // (capped at 100% for a huge spike). If shrinking, minimum 10% so the arc still
        // shows something.
        $fill = max(10, min(100, $pct < 0 ? max(10, 100 + $pct) : $pct));

        return [
            'current'    => $current,
            'previous'   => $previous,
            'pct'        => $pct,
            'fill'       => $fill,
            'compareTo'  => $this->compareTo,
            'currentLabel' => [
                'year'  => 'This Year',
                'month' => 'This Month',
                'week'  => 'This Week',
            ][$this->compareTo] ?? 'This Year',
            'prevLabel' => [
                'year'  => 'Last Year',
                'month' => 'Last Month',
                'week'  => 'Last Week',
            ][$this->compareTo] ?? 'Last Year',
        ];
    }
}
