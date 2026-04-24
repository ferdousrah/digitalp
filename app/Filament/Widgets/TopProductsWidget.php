<?php

namespace App\Filament\Widgets;

use App\Models\OrderItem;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class TopProductsWidget extends BaseWidget
{
    protected static ?string   $heading    = 'Best Selling Products';
    protected static ?int      $sort       = 7;
    protected array|string|int $columnSpan = 'full';

    public ?string $filter = 'all';

    public function getTableRecordKey(Model $record): string
    {
        return (string) ($record->product_id ?? $record->product_name ?? uniqid());
    }

    protected function getTableFiltersFormColumns(): int
    {
        return 1;
    }

    public function table(Table $table): Table
    {
        $filter = $this->filter ?? 'all';

        $query = OrderItem::query()
            ->selectRaw('
                product_id,
                product_name,
                product_image,
                SUM(quantity)          AS total_qty,
                SUM(subtotal)          AS total_revenue,
                COUNT(DISTINCT order_id) AS order_count,
                AVG(price)             AS avg_price
            ')
            ->groupBy('product_id', 'product_name', 'product_image')
            ->orderByDesc('total_qty');

        if ($filter !== 'all') {
            $since = match($filter) {
                '7'  => Carbon::now()->subDays(7),
                '30' => Carbon::now()->subDays(30),
                '90' => Carbon::now()->subDays(90),
                default => null,
            };
            if ($since) {
                $query->whereHas('order', fn ($q) => $q->where('created_at', '>=', $since));
            }
        }

        // Get max qty for progress bar calculation
        $maxQty = (clone $query)->limit(1)->value(DB::raw('SUM(quantity)')) ?? 1;

        return $table
            ->query($query->limit(15))
            ->filters([
                Tables\Filters\SelectFilter::make('filter')
                    ->label('Period')
                    ->options([
                        'all' => 'All Time',
                        '7'   => 'Last 7 Days',
                        '30'  => 'Last 30 Days',
                        '90'  => 'Last 90 Days',
                    ])
                    ->default('all')
                    ->query(function ($query, array $data) {
                        $val = $data['value'] ?? 'all';
                        $this->filter = $val;
                        if ($val === 'all') return $query;
                        $since = match($val) {
                            '7'  => Carbon::now()->subDays(7),
                            '30' => Carbon::now()->subDays(30),
                            '90' => Carbon::now()->subDays(90),
                            default => null,
                        };
                        if ($since) {
                            $query->whereHas('order', fn ($q) => $q->where('created_at', '>=', $since));
                        }
                        return $query;
                    }),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('rank')
                    ->label('#')
                    ->rowIndex()
                    ->badge()
                    ->color(fn ($state): string => match((int)$state) {
                        1       => 'warning',
                        2       => 'gray',
                        3       => 'danger',
                        default => 'primary',
                    }),

                Tables\Columns\ImageColumn::make('product_image')
                    ->label('')
                    ->width(44)->height(44)
                    ->defaultImageUrl(fn () => 'https://placehold.co/44x44/f3f4f6/9ca3af?text=P'),

                Tables\Columns\TextColumn::make('product_name')
                    ->label('Product')
                    ->limit(45)
                    ->weight(\Filament\Support\Enums\FontWeight::SemiBold),

                Tables\Columns\TextColumn::make('total_qty')
                    ->label('Units Sold')
                    ->numeric()
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('order_count')
                    ->label('Orders')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Revenue')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => '৳' . number_format($state, 2))
                    ->color('warning')
                    ->weight(\Filament\Support\Enums\FontWeight::Bold),

                Tables\Columns\TextColumn::make('avg_price')
                    ->label('Avg. Price')
                    ->formatStateUsing(fn ($state) => '৳' . number_format($state, 2))
                    ->color('gray'),
            ])
            ->searchable(false)
            ->paginated([10, 15, 25])
            ->defaultSort('total_qty', 'desc')
            ->emptyStateHeading('No sales data')
            ->emptyStateIcon('heroicon-o-shopping-bag');
    }
}
