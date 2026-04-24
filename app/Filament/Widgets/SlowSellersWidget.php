<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class SlowSellersWidget extends BaseWidget
{
    protected static ?string   $heading    = 'Slow Sellers';
    protected static ?int      $sort       = 9;
    protected array|string|int $columnSpan = 'full';

    public function table(Table $table): Table
    {
        $since = Carbon::now()->subDays(60);

        // Subquery: count units sold per product in last 60 days
        $soldSubquery = DB::table('order_items')
            ->selectRaw('product_id, SUM(quantity) as units_sold')
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.created_at', '>=', $since)
            ->groupBy('product_id');

        return $table
            ->query(
                Product::active()
                    ->leftJoinSub($soldSubquery, 'sales', 'sales.product_id', '=', 'products.id')
                    ->select('products.*', DB::raw('COALESCE(sales.units_sold, 0) as units_sold'))
                    ->where(DB::raw('COALESCE(sales.units_sold, 0)'), '<=', 3)
                    ->orderBy('units_sold', 'asc')
                    ->orderBy('products.created_at', 'asc')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('')
                    ->getStateUsing(fn (Product $r) => $r->getFirstMediaUrl('product_thumbnail'))
                    ->width(48)->height(48),

                Tables\Columns\TextColumn::make('name')
                    ->label('Product')
                    ->limit(40)
                    ->description(fn (Product $r) => $r->sku ?? ''),

                Tables\Columns\TextColumn::make('units_sold')
                    ->label('Units Sold (60 days)')
                    ->badge()
                    ->color(fn ($state): string => match(true) {
                        (int)$state === 0 => 'danger',
                        (int)$state <= 1  => 'warning',
                        default           => 'gray',
                    }),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Stock')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->formatStateUsing(fn ($state) => '৳' . number_format($state, 2)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Added')
                    ->date('d M Y')
                    ->color('gray'),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Category')
                    ->badge()
                    ->color('success')
                    ->separator(','),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->label('Edit')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn (Product $r) => route('filament.admin.resources.products.edit', $r))
                    ->openUrlInNewTab(),
            ])
            ->paginated([10, 25])
            ->searchable(false)
            ->emptyStateHeading('No slow sellers')
            ->emptyStateDescription('All active products have sold more than 3 units in the last 60 days.')
            ->emptyStateIcon('heroicon-o-trophy');
    }
}
