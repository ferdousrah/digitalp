<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Model;

class LowStockWidget extends BaseWidget
{
    protected static ?string   $heading   = 'Low Stock Alert';
    protected static ?int      $sort      = 8;
    protected array|string|int $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Product::query()
                    ->whereColumn('stock_quantity', '<=', 'min_stock_quantity')
                    ->where('in_stock', true)
                    ->orderBy('stock_quantity', 'asc')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->label('')
                    ->getStateUsing(fn (Product $r) => $r->getFirstMediaUrl('product_thumbnail'))
                    ->width(48)->height(48)
                    ->defaultImageUrl(asset('images/placeholder.png')),

                Tables\Columns\TextColumn::make('name')
                    ->label('Product')
                    ->searchable()
                    ->limit(40)
                    ->description(fn (Product $r) => $r->sku ?? ''),

                Tables\Columns\TextColumn::make('stock_quantity')
                    ->label('Current Stock')
                    ->badge()
                    ->color(fn (int $state): string => match(true) {
                        $state === 0  => 'danger',
                        $state <= 3   => 'warning',
                        default       => 'gray',
                    }),

                Tables\Columns\TextColumn::make('min_stock_quantity')
                    ->label('Min Stock')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Price')
                    ->money('BDT')
                    ->formatStateUsing(fn ($state) => '৳' . number_format($state, 2)),

                Tables\Columns\TextColumn::make('categories.name')
                    ->label('Category')
                    ->badge()
                    ->color('success')
                    ->separator(','),

                Tables\Columns\IconColumn::make('in_stock')
                    ->label('In Stock')
                    ->boolean(),
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
            ->emptyStateHeading('No low stock products')
            ->emptyStateDescription('All products have stock above their minimum threshold.')
            ->emptyStateIcon('heroicon-o-check-circle');
    }
}
