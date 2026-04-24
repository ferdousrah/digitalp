<?php

namespace App\Filament\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TopCategoriesWidget extends Widget
{
    protected static string $view = 'filament.widgets.top-categories-widget';
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = ['default' => 'full', 'md' => 1, 'xl' => 2];

    public ?string $period = 'week';

    protected $listeners = ['updateTopCategoriesPeriod' => 'setPeriod'];

    public function setPeriod(string $period): void
    {
        $this->period = $period;
    }

    protected function getViewData(): array
    {
        $since = match ($this->period) {
            'week'  => Carbon::now()->subWeek(),
            'month' => Carbon::now()->subMonth(),
            'year'  => Carbon::now()->subYear(),
            default => Carbon::now()->subWeek(),
        };

        $rows = DB::table('order_items')
            ->join('products', 'products.id', '=', 'order_items.product_id')
            ->join('category_product', 'category_product.product_id', '=', 'products.id')
            ->join('categories', 'categories.id', '=', 'category_product.category_id')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.payment_status', 'paid')
            ->where('orders.created_at', '>=', $since)
            ->groupBy('categories.id', 'categories.name')
            ->select('categories.name', DB::raw('SUM(order_items.quantity * order_items.price) as revenue'))
            ->orderByDesc('revenue')
            ->limit(3)
            ->get();

        $max = (float) ($rows->max('revenue') ?: 1);

        // Give each bubble a size (in %) based on revenue relative to the biggest
        $palette = [
            ['bg' => '#7c7dfa', 'text' => '#fff'],  // purple
            ['bg' => '#86c267', 'text' => '#fff'],  // green
            ['bg' => '#e5e7eb', 'text' => '#1f2937'],// gray
        ];

        $bubbles = [];
        foreach ($rows as $i => $row) {
            $ratio = max(0.55, (float) $row->revenue / $max);
            $bubbles[] = [
                'name'    => $row->name,
                'revenue' => '৳' . number_format((float) $row->revenue, 0),
                'size'    => round($ratio * 100),  // %-of-max for visual size
                'bg'      => $palette[$i]['bg'] ?? '#9ca3af',
                'text'    => $palette[$i]['text'] ?? '#fff',
            ];
        }

        return [
            'bubbles' => $bubbles,
            'period'  => $this->period,
        ];
    }
}
