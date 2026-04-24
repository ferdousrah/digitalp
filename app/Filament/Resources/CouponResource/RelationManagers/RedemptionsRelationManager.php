<?php

namespace App\Filament\Resources\CouponResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class RedemptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'redemptions';
    protected static ?string $recordTitleAttribute = 'id';
    protected static ?string $title = 'Redemptions';

    public function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('used_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('used_at')->dateTime('M d, Y h:i A')->sortable(),
                Tables\Columns\TextColumn::make('order.order_number')
                    ->label('Order')
                    ->url(fn ($record) => $record->order ? route('filament.admin.resources.orders.view', $record->order) : null)
                    ->placeholder('—'),
                Tables\Columns\TextColumn::make('customer_phone')->searchable(),
                Tables\Columns\TextColumn::make('subtotal_before')->money('BDT')->label('Subtotal'),
                Tables\Columns\TextColumn::make('discount_applied')->money('BDT')->weight('bold')->color('success'),
            ])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }

    public function isReadOnly(): bool
    {
        return true;
    }
}
