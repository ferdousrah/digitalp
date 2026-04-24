<?php

namespace App\Filament\Widgets;

use App\Models\Product;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class PopularProductsWidget extends BaseWidget
{
    protected static ?string $heading = 'Popular Products';

    protected static ?int $sort = 3;

    protected int | string | array $columnSpan = 'full';

    protected static bool $isLazy = false;

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query()->orderByDesc('view_count')->limit(5))
            ->columns([
                SpatieMediaLibraryImageColumn::make('product_thumbnail')
                    ->collection('product_thumbnail')
                    ->circular(),
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('sku')->label('SKU'),
                Tables\Columns\TextColumn::make('price')->money('BDT'),
                Tables\Columns\TextColumn::make('view_count')->label('Views')->sortable(),
            ]);
    }
}
