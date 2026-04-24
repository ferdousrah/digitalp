<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentOrdersWidget extends BaseWidget
{
    protected static ?string $heading   = 'Recent Orders';
    protected static ?int    $sort      = 6;
    protected array|string|int $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Order::latest()->limit(10))
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->fontFamily('mono')
                    ->url(fn (Order $r) => route('filament.admin.resources.orders.edit', $r))
                    ->color('primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('shipping_name')
                    ->label('Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('shipping_district')
                    ->label('District')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'cod'    => 'success',
                        'bkash'  => 'danger',
                        'online' => 'info',
                        default  => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'cod'    => 'COD',
                        'bkash'  => 'bKash',
                        'online' => 'Online',
                        default  => ucfirst($state),
                    }),

                Tables\Columns\TextColumn::make('total')
                    ->label('Total')
                    ->money('BDT', locale: 'bn')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => '৳' . number_format($state, 2)),

                Tables\Columns\TextColumn::make('payment_status')
                    ->label('Payment')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'      => 'success',
                        'pending'   => 'warning',
                        'failed'    => 'danger',
                        'cancelled' => 'gray',
                        default     => 'gray',
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'    => 'warning',
                        'processing' => 'info',
                        'shipped'    => 'primary',
                        'delivered'  => 'success',
                        'cancelled'  => 'danger',
                        'completed'  => 'success',
                        default      => 'gray',
                    }),
            ])
            ->searchable(false)
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Order $r) => route('filament.admin.resources.orders.edit', $r)),
            ])
            ->paginated(false);
    }
}
