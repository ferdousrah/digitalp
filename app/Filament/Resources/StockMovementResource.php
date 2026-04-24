<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesWithPermission;
use App\Filament\Concerns\ExportsToCsv;
use App\Filament\Resources\StockMovementResource\Pages;
use App\Models\Product;
use App\Models\StockMovement;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class StockMovementResource extends Resource
{
    use AuthorizesWithPermission;
    use ExportsToCsv;
    protected static ?string $permissionKey = 'stock_movements';

    protected static ?string $model = StockMovement::class;
    protected static ?string $navigationIcon = 'heroicon-o-queue-list';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Stock Movements';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        // Read-only resource — no form needed
        return $form->schema([]);
    }

    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('When')
                    ->dateTime('M d, Y h:i A')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->weight('semibold')
                    ->wrap(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => StockMovement::types()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'purchase'   => 'success',
                        'sale'       => 'info',
                        'adjustment' => 'warning',
                        'return'     => 'primary',
                        default      => 'gray',
                    }),
                Tables\Columns\TextColumn::make('quantity')
                    ->label('Change')
                    ->formatStateUsing(fn ($state) => ($state > 0 ? '+' : '') . number_format((int) $state))
                    ->color(fn ($state) => $state > 0 ? 'success' : 'danger')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('balance_after')
                    ->label('Balance')
                    ->numeric()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('reference_display')
                    ->label('Reference')
                    ->getStateUsing(function ($record) {
                        if ($record->reference_type === \App\Models\PurchaseOrder::class) {
                            $po = \App\Models\PurchaseOrder::find($record->reference_id);
                            return $po ? "PO #{$po->po_number}" : '—';
                        }
                        if ($record->reference_type === \App\Models\Order::class) {
                            $order = \App\Models\Order::find($record->reference_id);
                            return $order ? "Order #{$order->order_number}" : '—';
                        }
                        return '—';
                    }),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('By')
                    ->placeholder('System')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('notes')
                    ->limit(40)
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')->options(StockMovement::types()),
                SelectFilter::make('product_id')
                    ->label('Product')
                    ->options(fn () => Product::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->actions([])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    static::csvExportBulkAction(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockMovements::route('/'),
        ];
    }
}
