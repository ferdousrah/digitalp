<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesWithPermission;
use App\Filament\Concerns\ExportsToCsv;
use App\Filament\Resources\PurchaseOrderResource\Pages;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\InventoryService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PurchaseOrderResource extends Resource
{
    use AuthorizesWithPermission;
    use ExportsToCsv;
    protected static ?string $permissionKey = 'purchase_orders';

    protected static ?string $model = PurchaseOrder::class;
    protected static ?string $recordTitleAttribute = 'po_number';
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Purchase Orders';
    protected static ?int $navigationSort = 2;

    public static function getGloballySearchableAttributes(): array
    {
        return ['po_number', 'supplier.name'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Purchase Order')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('po_number')
                        ->label('PO Number')
                        ->disabled()
                        ->dehydrated()
                        ->default(fn () => 'PO-' . strtoupper(\Illuminate\Support\Str::random(8)))
                        ->unique(ignoreRecord: true),
                    Forms\Components\Select::make('supplier_id')
                        ->label('Supplier')
                        ->options(Supplier::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->createOptionForm([
                            Forms\Components\TextInput::make('name')->required()->maxLength(255),
                            Forms\Components\TextInput::make('phone'),
                        ])
                        ->createOptionUsing(fn (array $data) => Supplier::create($data)->getKey()),
                    Forms\Components\Select::make('status')
                        ->options(PurchaseOrder::statuses())
                        ->default('draft')
                        ->disabled(fn ($record) => $record && in_array($record->status, ['received', 'partial']))
                        ->helperText('Status is auto-set when goods are received'),
                    Forms\Components\DatePicker::make('order_date')
                        ->required()
                        ->default(now())
                        ->native(false),
                    Forms\Components\DatePicker::make('expected_date')
                        ->label('Expected delivery')
                        ->native(false),
                    Forms\Components\DatePicker::make('received_date')
                        ->native(false)
                        ->disabled(),
                ]),

            Forms\Components\Section::make('Line Items')
                ->description('Products you are purchasing from the supplier.')
                ->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Forms\Components\Select::make('product_id')
                                ->label('Product (name or SKU / barcode)')
                                ->searchable()
                                ->required()
                                ->getSearchResultsUsing(fn (string $search) => Product::query()
                                    ->where('is_active', true)
                                    ->where(function ($q) use ($search) {
                                        $q->where('name', 'like', "%{$search}%")
                                          ->orWhere('sku', 'like', "%{$search}%");
                                    })
                                    ->limit(50)
                                    ->get()
                                    ->mapWithKeys(fn ($p) => [$p->id => $p->sku ? "[{$p->sku}] {$p->name}" : $p->name])
                                    ->toArray())
                                ->getOptionLabelUsing(fn ($value) => ($p = Product::find($value)) ? ($p->sku ? "[{$p->sku}] {$p->name}" : $p->name) : null)
                                ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                                    if ($p = Product::find($state)) {
                                        $set('unit_cost', (float) $p->cost_price);
                                    }
                                })
                                ->live()
                                ->columnSpan(4),
                            Forms\Components\TextInput::make('quantity_ordered')
                                ->label('Qty Ordered')
                                ->numeric()
                                ->required()
                                ->minValue(1)
                                ->default(1)
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('quantity_received')
                                ->label('Qty Received')
                                ->numeric()
                                ->default(0)
                                ->disabled()
                                ->dehydrated()
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('unit_cost')
                                ->label('Unit Cost')
                                ->numeric()
                                ->prefix('৳')
                                ->required()
                                ->minValue(0)
                                ->columnSpan(2),
                        ])
                        ->columns(10)
                        ->defaultItems(1)
                        ->addActionLabel('Add item')
                        ->reorderable(false)
                        ->disableItemDeletion(fn ($record) => $record && $record->status === 'received')
                        ->itemLabel(fn (array $state): ?string =>
                            $state['product_id']
                                ? Product::find($state['product_id'])?->name . ' × ' . ($state['quantity_ordered'] ?? 0)
                                : null
                        ),
                ]),

            Forms\Components\Section::make('Totals')
                ->columns(4)
                ->schema([
                    Forms\Components\TextInput::make('tax')->numeric()->prefix('৳')->default(0)->minValue(0),
                    Forms\Components\TextInput::make('discount')->numeric()->prefix('৳')->default(0)->minValue(0),
                    Forms\Components\TextInput::make('shipping_cost')->label('Shipping')->numeric()->prefix('৳')->default(0)->minValue(0),
                    Forms\Components\Placeholder::make('total_display')
                        ->label('Grand Total')
                        ->content(fn ($record) => $record ? '৳' . number_format((float) $record->total, 2) : '—'),
                ]),

            Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => Pages\EditPurchaseOrder::getUrl(['record' => $record]))
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('po_number')
                    ->label('PO #')
                    ->searchable()
                    ->weight('semibold'),
                Tables\Columns\TextColumn::make('supplier.name')
                    ->label('Supplier')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => PurchaseOrder::statuses()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        'draft'     => 'gray',
                        'ordered'   => 'info',
                        'partial'   => 'warning',
                        'received'  => 'success',
                        'cancelled' => 'danger',
                        default     => 'gray',
                    }),
                Tables\Columns\TextColumn::make('order_date')->date('M d, Y')->sortable(),
                Tables\Columns\TextColumn::make('expected_date')->date('M d, Y')->toggleable(),
                Tables\Columns\TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items')
                    ->badge(),
                Tables\Columns\TextColumn::make('total')
                    ->money('BDT')
                    ->sortable()
                    ->weight('bold'),
            ])
            ->filters([
                SelectFilter::make('status')->options(PurchaseOrder::statuses()),
                SelectFilter::make('supplier_id')
                    ->label('Supplier')
                    ->relationship('supplier', 'name')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('receiveAll')
                    ->label('Receive All')
                    ->icon('heroicon-o-arrow-down-on-square')
                    ->color('success')
                    ->visible(fn ($record) => in_array($record->status, ['draft', 'ordered', 'partial']))
                    ->requiresConfirmation()
                    ->modalHeading('Receive all outstanding items?')
                    ->modalDescription('Stock will be added for every item with an outstanding quantity. This also updates each product\'s cost price.')
                    ->action(function (PurchaseOrder $record) {
                        foreach ($record->items as $item) {
                            InventoryService::receivePurchaseOrderItem($item);
                        }
                        $record->refresh()->recomputeStatus();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    static::csvExportBulkAction(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPurchaseOrders::route('/'),
            'create' => Pages\CreatePurchaseOrder::route('/create'),
            'edit'   => Pages\EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
