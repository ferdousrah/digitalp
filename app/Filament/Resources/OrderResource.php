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
                        ->options(\App\Models\Order::statuses())
                        ->required()
                        ->live(),

                    Forms\Components\Select::make('payment_status')
                        ->options(\App\Models\Order::paymentStatuses())
                        ->required(),
                ]),

            Forms\Components\Section::make('Shipping & Fulfilment')
                ->description('Add courier + tracking info when you hand the parcel over.')
                ->columns(2)
                ->schema([
                    Forms\Components\Select::make('courier_service')
                        ->label('Courier')
                        ->options(\App\Models\Order::couriers())
                        ->searchable()
                        ->placeholder('Select courier'),
                    Forms\Components\TextInput::make('tracking_number')
                        ->label('Tracking Number')
                        ->maxLength(100),
                    Forms\Components\DateTimePicker::make('shipped_at')
                        ->label('Shipped at')
                        ->native(false),
                    Forms\Components\DateTimePicker::make('delivered_at')
                        ->label('Delivered at')
                        ->native(false),
                ]),

            Forms\Components\Section::make('Internal Notes')
                ->columns(1)
                ->schema([
                    Forms\Components\Textarea::make('admin_notes')
                        ->label('Admin-only notes')
                        ->rows(3)
                        ->helperText('Visible to staff only. Not shown to customer.'),
                    Forms\Components\Textarea::make('notes')
                        ->label('Customer Notes (from checkout)')
                        ->rows(2)
                        ->disabled(),
                ]),

            Forms\Components\Section::make('Refund')
                ->collapsed(fn ($record) => !$record?->refund_amount)
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('refund_amount')
                        ->numeric()
                        ->prefix('৳')
                        ->minValue(0),
                    Forms\Components\DateTimePicker::make('refunded_at')
                        ->native(false),
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

            Infolists\Components\Section::make('Fulfilment')
                ->icon('heroicon-o-truck')
                ->columns(3)
                ->schema([
                    Infolists\Components\TextEntry::make('courier_service')
                        ->label('Courier')
                        ->formatStateUsing(fn ($state) => $state ? (\App\Models\Order::couriers()[$state] ?? $state) : '—'),
                    Infolists\Components\TextEntry::make('tracking_number')
                        ->label('Tracking #')
                        ->copyable()
                        ->placeholder('—'),
                    Infolists\Components\TextEntry::make('shipped_at')
                        ->label('Shipped at')
                        ->dateTime('d M Y, h:i A')
                        ->placeholder('—'),
                    Infolists\Components\TextEntry::make('delivered_at')
                        ->label('Delivered at')
                        ->dateTime('d M Y, h:i A')
                        ->placeholder('—'),
                    Infolists\Components\TextEntry::make('cancelled_at')
                        ->label('Cancelled at')
                        ->dateTime('d M Y, h:i A')
                        ->placeholder('—'),
                    Infolists\Components\TextEntry::make('refund_amount')
                        ->label('Refund')
                        ->money('BDT')
                        ->placeholder('—'),
                ])
                ->hidden(fn (Order $record) => !$record->courier_service && !$record->tracking_number && !$record->shipped_at && !$record->cancelled_at && !$record->refund_amount),

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

            Infolists\Components\Section::make('Activity Timeline')
                ->icon('heroicon-o-clock')
                ->schema([
                    Infolists\Components\RepeatableEntry::make('activities')
                        ->hiddenLabel()
                        ->columns(12)
                        ->contained(false)
                        ->schema([
                            Infolists\Components\TextEntry::make('created_at')
                                ->hiddenLabel()
                                ->since()
                                ->columnSpan(2),
                            Infolists\Components\TextEntry::make('title')
                                ->hiddenLabel()
                                ->weight('semibold')
                                ->columnSpan(4),
                            Infolists\Components\TextEntry::make('description')
                                ->hiddenLabel()
                                ->color('gray')
                                ->columnSpan(4),
                            Infolists\Components\TextEntry::make('user.name')
                                ->hiddenLabel()
                                ->badge()
                                ->color('gray')
                                ->prefix('by ')
                                ->placeholder('System')
                                ->columnSpan(2),
                        ]),
                ])
                ->hidden(fn (Order $record) => $record->activities()->count() === 0),
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
                Tables\Filters\SelectFilter::make('status')->options(\App\Models\Order::statuses()),
                Tables\Filters\SelectFilter::make('payment_status')->options(\App\Models\Order::paymentStatuses()),
                Tables\Filters\SelectFilter::make('payment_method')->options([
                    'cod'    => 'Cash On Delivery',
                    'bkash'  => 'Bkash',
                    'online' => 'Online Payment',
                ]),
                Tables\Filters\Filter::make('today')
                    ->label('Today')
                    ->query(fn ($q) => $q->whereDate('created_at', today()))
                    ->toggle(),
                Tables\Filters\Filter::make('this_week')
                    ->label('This week')
                    ->query(fn ($q) => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]))
                    ->toggle(),
                Tables\Filters\Filter::make('awaiting_payment')
                    ->label('Awaiting payment')
                    ->query(fn ($q) => $q->where('payment_status', 'pending'))
                    ->toggle(),
                Tables\Filters\Filter::make('awaiting_shipment')
                    ->label('Awaiting shipment')
                    ->query(fn ($q) => $q->whereIn('status', ['pending', 'processing']))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\Action::make('markProcessing')
                        ->label('Mark as Processing')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->color('info')
                        ->visible(fn ($record) => in_array($record->status, ['pending']))
                        ->requiresConfirmation()
                        ->action(fn ($record) => $record->update(['status' => 'processing'])),
                    Tables\Actions\Action::make('markShipped')
                        ->label('Mark as Shipped')
                        ->icon('heroicon-o-truck')
                        ->color('primary')
                        ->visible(fn ($record) => in_array($record->status, ['pending', 'processing']))
                        ->form([
                            Forms\Components\Select::make('courier_service')
                                ->label('Courier')
                                ->options(\App\Models\Order::couriers())
                                ->required(),
                            Forms\Components\TextInput::make('tracking_number')
                                ->label('Tracking number')
                                ->required()
                                ->maxLength(100),
                        ])
                        ->action(fn ($record, array $data) => $record->update([
                            'status'          => 'shipped',
                            'courier_service' => $data['courier_service'],
                            'tracking_number' => $data['tracking_number'],
                        ])),
                    Tables\Actions\Action::make('markDelivered')
                        ->label('Mark as Delivered')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->visible(fn ($record) => $record->status === 'shipped')
                        ->requiresConfirmation()
                        ->action(fn ($record) => $record->update(['status' => 'delivered', 'payment_status' => 'paid'])),
                    Tables\Actions\Action::make('markPaid')
                        ->label('Mark as Paid')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->visible(fn ($record) => $record->payment_status !== 'paid')
                        ->requiresConfirmation()
                        ->action(fn ($record) => $record->update(['payment_status' => 'paid'])),
                    Tables\Actions\Action::make('cancel')
                        ->label('Cancel Order')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->visible(fn ($record) => !in_array($record->status, ['delivered', 'cancelled', 'refunded']))
                        ->requiresConfirmation()
                        ->action(fn ($record) => $record->update(['status' => 'cancelled'])),
                    Tables\Actions\Action::make('printInvoice')
                        ->label('Print Invoice')
                        ->icon('heroicon-o-printer')
                        ->color('gray')
                        ->url(fn ($record) => route('admin.orders.invoice', $record))
                        ->openUrlInNewTab(),
                ])->label('Quick actions')->icon('heroicon-m-ellipsis-vertical')->color('gray'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('bulkUpdateStatus')
                        ->label('Change Status')
                        ->icon('heroicon-o-arrow-path')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->options(\App\Models\Order::statuses())
                                ->required(),
                        ])
                        ->action(function ($records, array $data) {
                            foreach ($records as $record) {
                                $record->update(['status' => $data['status']]);
                            }
                        })
                        ->deselectRecordsAfterCompletion(),
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
