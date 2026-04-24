<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

use App\Filament\Concerns\AuthorizesWithPermission;
use App\Filament\Concerns\ExportsToCsv;

class OrderResource extends Resource
{
    use AuthorizesWithPermission;
    use ExportsToCsv;
    protected static ?string $permissionKey = 'orders';

    protected static ?string $model = Order::class;
    protected static ?string $recordTitleAttribute = 'order_number';

    public static function getGloballySearchableAttributes(): array
    {
        return ['order_number', 'shipping_name', 'shipping_phone', 'billing_name'];
    }
    protected static ?string $navigationIcon  = 'heroicon-o-shopping-bag';
    protected static ?string $navigationGroup = 'Shop';
    protected static ?string $navigationLabel = 'Orders';
    protected static ?int    $navigationSort  = 1;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Order Status')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('status')
                        ->options([
                            'pending'    => 'Pending',
                            'processing' => 'Processing',
                            'shipped'    => 'Shipped',
                            'delivered'  => 'Delivered',
                            'cancelled'  => 'Cancelled',
                        ])
                        ->required(),

                    Forms\Components\Select::make('payment_status')
                        ->options([
                            'pending' => 'Pending',
                            'paid'    => 'Paid',
                            'failed'  => 'Failed',
                        ])
                        ->required(),
                ]),

            Forms\Components\Section::make('Admin Notes')
                ->schema([
                    Forms\Components\Textarea::make('notes')
                        ->label('Order Notes')
                        ->rows(3)
                        ->disabled(),
                ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([

            Infolists\Components\Section::make('Order Summary')
                ->columns(4)
                ->schema([
                    Infolists\Components\TextEntry::make('order_number')
                        ->label('Order #')
                        ->weight('bold')
                        ->color('warning'),

                    Infolists\Components\TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'pending'    => 'warning',
                            'processing' => 'info',
                            'shipped'    => 'primary',
                            'delivered'  => 'success',
                            'cancelled'  => 'danger',
                            default      => 'gray',
                        }),

                    Infolists\Components\TextEntry::make('payment_method_label')
                        ->label('Payment Method'),

                    Infolists\Components\TextEntry::make('payment_status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'paid'    => 'success',
                            'failed'  => 'danger',
                            default   => 'warning',
                        }),
                ]),

            Infolists\Components\Section::make('Order Totals')
                ->columns(4)
                ->schema([
                    Infolists\Components\TextEntry::make('subtotal')
                        ->label('Items Subtotal')
                        ->money('BDT'),

                    Infolists\Components\TextEntry::make('delivery_cost')
                        ->label('Delivery Charge')
                        ->money('BDT'),

                    Infolists\Components\TextEntry::make('coupon_discount')
                        ->label('Coupon Discount')
                        ->money('BDT'),

                    Infolists\Components\TextEntry::make('total')
                        ->label('Grand Total')
                        ->money('BDT')
                        ->weight('bold')
                        ->color('warning'),
                ]),

            Infolists\Components\Section::make('Shipping Address')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('shipping_name')->label('Name'),
                    Infolists\Components\TextEntry::make('shipping_phone')->label('Phone'),
                    Infolists\Components\TextEntry::make('shipping_district')->label('District'),
                    Infolists\Components\TextEntry::make('shipping_thana')->label('Thana'),
                    Infolists\Components\TextEntry::make('shipping_address')->label('Address')->columnSpanFull(),
                ]),

            Infolists\Components\Section::make('Billing Address')
                ->columns(2)
                ->schema([
                    Infolists\Components\TextEntry::make('billing_name')->label('Name'),
                    Infolists\Components\TextEntry::make('billing_phone')->label('Phone'),
                    Infolists\Components\TextEntry::make('billing_district')->label('District'),
                    Infolists\Components\TextEntry::make('billing_thana')->label('Thana'),
                    Infolists\Components\TextEntry::make('billing_address')->label('Address')->columnSpanFull(),
                ]),

            Infolists\Components\Section::make('Notes')
                ->schema([
                    Infolists\Components\TextEntry::make('notes')
                        ->label('Special Notes')
                        ->placeholder('No notes')
                        ->columnSpanFull(),
                ])
                ->hidden(fn (Order $record) => empty($record->notes)),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => Pages\EditOrder::getUrl(['record' => $record]))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->label('Order #')
                    ->searchable()
                    ->weight('bold')
                    ->color('warning'),

                Tables\Columns\TextColumn::make('shipping_name')
                    ->label('Customer')
                    ->searchable(),

                Tables\Columns\TextColumn::make('shipping_phone')
                    ->label('Phone')
                    ->searchable(),

                Tables\Columns\TextColumn::make('payment_method_label')
                    ->label('Payment'),

                Tables\Columns\TextColumn::make('total')
                    ->money('BDT')
                    ->label('Total')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending'    => 'warning',
                        'processing' => 'info',
                        'shipped'    => 'primary',
                        'delivered'  => 'success',
                        'cancelled'  => 'danger',
                        default      => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'paid'    => 'success',
                        'failed'  => 'danger',
                        default   => 'warning',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending'    => 'Pending',
                        'processing' => 'Processing',
                        'shipped'    => 'Shipped',
                        'delivered'  => 'Delivered',
                        'cancelled'  => 'Cancelled',
                    ]),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid'    => 'Paid',
                        'failed'  => 'Failed',
                    ]),

                Tables\Filters\SelectFilter::make('payment_method')
                    ->options([
                        'cod'    => 'Cash On Delivery',
                        'bkash'  => 'Bkash',
                        'online' => 'Online Payment',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    static::csvExportBulkAction(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            OrderResource\RelationManagers\ItemsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOrders::route('/'),
            'view'   => Pages\ViewOrder::route('/{record}'),
            'edit'   => Pages\EditOrder::route('/{record}/edit'),
        ];
    }
}
