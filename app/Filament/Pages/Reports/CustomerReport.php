<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Pages\Reports\Concerns\AuthorizesReport;
use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerReport extends Page implements HasForms
{
    use AuthorizesReport;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Customer Report';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $title = 'Customer Report';
    protected static ?int $navigationSort = 6;
    protected static string $routePath = 'reports/customers';
    protected static ?string $slug = 'reports/customers';
    protected static string $view = 'filament.pages.reports.customers';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'from' => Carbon::now()->subYear()->toDateString(),
            'to'   => Carbon::now()->toDateString(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('from')->native(false)->required(),
                DatePicker::make('to')->native(false)->required(),
            ])
            ->statePath('data')
            ->columns(2);
    }

    public function getReport(): array
    {
        $from = Carbon::parse($this->data['from'] ?? Carbon::now()->subYear())->startOfDay();
        $to   = Carbon::parse($this->data['to']   ?? Carbon::now())->endOfDay();

        $customers = DB::table('orders')
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('
                shipping_phone as phone,
                MAX(shipping_name) as name,
                COUNT(*) as total_orders,
                SUM(CASE WHEN payment_status = "paid" THEN total ELSE 0 END) as total_spent,
                MAX(created_at) as last_order
            ')
            ->whereNotNull('shipping_phone')
            ->groupBy('shipping_phone')
            ->orderByDesc('total_spent')
            ->limit(100)
            ->get();

        return [
            'customers'  => $customers,
            'count'      => $customers->count(),
            'topSpender' => $customers->first(),
            'totalSpend' => (float) $customers->sum('total_spent'),
        ];
    }
}
