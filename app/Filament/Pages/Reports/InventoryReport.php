<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Pages\Reports\Concerns\AuthorizesReport;
use App\Models\Product;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class InventoryReport extends Page implements HasForms
{
    use AuthorizesReport;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';
    protected static ?string $navigationLabel = 'Inventory Report';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $title = 'Inventory Report';
    protected static ?int $navigationSort = 5;
    protected static string $routePath = 'reports/inventory';
    protected static ?string $slug = 'reports/inventory';
    protected static string $view = 'filament.pages.reports.inventory';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'filter' => 'all',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('filter')
                    ->label('Filter')
                    ->options([
                        'all'       => 'All products',
                        'in_stock'  => 'In stock only',
                        'out_stock' => 'Out of stock',
                        'low_stock' => 'Low stock (≤ min)',
                    ])
                    ->default('all')
                    ->live(),
            ])
            ->statePath('data')
            ->columns(['default' => 1, 'md' => 3]);
    }

    public function getReport(): array
    {
        $query = Product::query()->with('brand');

        $filter = $this->data['filter'] ?? 'all';
        if ($filter === 'in_stock')  $query->where('in_stock', true);
        if ($filter === 'out_stock') $query->where(fn ($q) => $q->where('in_stock', false)->orWhere('stock_quantity', '<=', 0));
        if ($filter === 'low_stock') $query->where('in_stock', true)->whereColumn('stock_quantity', '<=', 'min_stock_quantity');

        $products = $query->orderBy('name')->get();

        $totalUnits       = (int) $products->sum('stock_quantity');
        $totalValueCost   = (float) $products->sum(fn ($p) => (float) $p->cost_price * (int) $p->stock_quantity);
        $totalValueRetail = (float) $products->sum(fn ($p) => (float) $p->price * (int) $p->stock_quantity);

        $lowStockCount    = $products->filter(fn ($p) => $p->in_stock && $p->stock_quantity <= $p->min_stock_quantity)->count();
        $outCount         = $products->filter(fn ($p) => !$p->in_stock || $p->stock_quantity <= 0)->count();

        return [
            'products'         => $products,
            'totalUnits'       => $totalUnits,
            'totalValueCost'   => $totalValueCost,
            'totalValueRetail' => $totalValueRetail,
            'potentialProfit'  => $totalValueRetail - $totalValueCost,
            'lowStockCount'    => $lowStockCount,
            'outCount'         => $outCount,
            'productCount'     => $products->count(),
        ];
    }
}
