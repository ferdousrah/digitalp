<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Order Items';

    public function isReadOnly(): bool
    {
        return true;
    }

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('product_image')
                    ->label('Image')
                    ->width(52)
                    ->height(52)
                    ->defaultImageUrl(fn () => null),

                Tables\Columns\TextColumn::make('product_name')
                    ->label('Product')
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('price')
                    ->label('Unit Price')
                    ->money('BDT'),

                Tables\Columns\TextColumn::make('quantity')
                    ->label('Qty')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('subtotal')
                    ->label('Subtotal')
                    ->money('BDT')
                    ->weight('bold'),
            ])
            ->paginated(false)
            ->striped();
    }
}
