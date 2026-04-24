<?php

namespace App\Filament\Pages\Inventory;

use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Str;

class ReorderSuggestions extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationLabel = 'Reorder Suggestions';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $title = 'Reorder Suggestions';
    protected static ?int $navigationSort = 5;
    protected static string $routePath = 'inventory/reorder';
    protected static ?string $slug = 'inventory/reorder';
    protected static string $view = 'filament.pages.inventory.reorder-suggestions';

    public array $selected = [];
    public array $quantities = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->can('purchase_orders.create'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public function getSubheading(): ?string
    {
        return 'Products at or below their minimum stock level — ready to re-order.';
    }

    /**
     * Products that need restocking.
     */
    public function getCandidates()
    {
        return Product::query()
            ->where('is_active', true)
            ->whereColumn('stock_quantity', '<=', 'min_stock_quantity')
            ->with('brand')
            ->orderByRaw('(stock_quantity - min_stock_quantity) ASC')
            ->get()
            ->map(function ($p) {
                // Suggested order quantity = (2× min) - current, minimum 1
                $suggested = max(1, ((int) $p->min_stock_quantity * 2) - (int) $p->stock_quantity);
                return (object) [
                    'id'          => $p->id,
                    'name'        => $p->name,
                    'sku'         => $p->sku,
                    'brand'       => $p->brand?->name,
                    'stock'       => (int) $p->stock_quantity,
                    'min'         => (int) $p->min_stock_quantity,
                    'cost_price'  => (float) $p->cost_price,
                    'suggested'   => $suggested,
                ];
            });
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createPO')
                ->label('Create PO from Selected')
                ->icon('heroicon-o-clipboard-document-list')
                ->color('success')
                ->modalHeading('Create a Purchase Order')
                ->form([
                    Select::make('supplier_id')
                        ->label('Supplier')
                        ->options(Supplier::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                        ->searchable()
                        ->required()
                        ->createOptionForm([
                            \Filament\Forms\Components\TextInput::make('name')->required(),
                            \Filament\Forms\Components\TextInput::make('phone'),
                        ])
                        ->createOptionUsing(fn ($data) => Supplier::create($data)->getKey()),
                ])
                ->action(function (array $data) {
                    $ids = array_keys(array_filter($this->selected ?? []));
                    if (empty($ids)) {
                        \Filament\Notifications\Notification::make()
                            ->title('No products selected')
                            ->warning()->send();
                        return;
                    }

                    $po = PurchaseOrder::create([
                        'po_number'   => 'PO-' . strtoupper(Str::random(8)),
                        'supplier_id' => $data['supplier_id'],
                        'status'      => 'draft',
                        'order_date'  => now()->toDateString(),
                        'created_by'  => auth()->id(),
                    ]);

                    foreach ($ids as $productId) {
                        $product = Product::find($productId);
                        if (!$product) continue;
                        $qty = (int) ($this->quantities[$productId] ?? max(1, ((int) $product->min_stock_quantity * 2) - (int) $product->stock_quantity));

                        PurchaseOrderItem::create([
                            'purchase_order_id' => $po->id,
                            'product_id'        => $product->id,
                            'quantity_ordered'  => $qty,
                            'quantity_received' => 0,
                            'unit_cost'         => (float) $product->cost_price,
                            'subtotal'          => (float) $product->cost_price * $qty,
                        ]);
                    }
                    $po->recomputeTotals();

                    $this->selected = [];
                    $this->quantities = [];

                    \Filament\Notifications\Notification::make()
                        ->title('Draft PO created')
                        ->body('Purchase Order ' . $po->po_number . ' is now in Draft. Review and mark as Ordered when you send it to the supplier.')
                        ->success()->send();

                    $this->redirect(\App\Filament\Resources\PurchaseOrderResource::getUrl('edit', ['record' => $po]));
                }),
        ];
    }

    public function selectAll(): void
    {
        foreach ($this->getCandidates() as $c) {
            $this->selected[$c->id] = true;
            $this->quantities[$c->id] = $c->suggested;
        }
    }

    public function clearAll(): void
    {
        $this->selected = [];
        $this->quantities = [];
    }
}
