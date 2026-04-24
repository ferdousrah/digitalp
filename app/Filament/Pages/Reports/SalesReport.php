<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Pages\Reports\Concerns\AuthorizesReport;
use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class SalesReport extends Page implements HasForms
{
    use AuthorizesReport;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';
    protected static ?string $navigationLabel = 'Sales Report';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $title = 'Sales Report';
    protected static ?int $navigationSort = 3;
    protected static string $routePath = 'reports/sales';
    protected static ?string $slug = 'reports/sales';
    protected static string $view = 'filament.pages.reports.sales';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'from'           => Carbon::now()->subMonth()->toDateString(),
            'to'             => Carbon::now()->toDateString(),
            'status'         => null,
            'payment_status' => null,
            'payment_method' => null,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('from')->native(false)->required(),
                DatePicker::make('to')->native(false)->required(),
                Select::make('status')
                    ->options([
                        'pending'    => 'Pending',
                        'processing' => 'Processing',
                        'shipped'    => 'Shipped',
                        'delivered'  => 'Delivered',
                        'completed'  => 'Completed',
                        'cancelled'  => 'Cancelled',
                    ])
                    ->placeholder('All statuses'),
                Select::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid'    => 'Paid',
                        'failed'  => 'Failed',
                        'refunded'=> 'Refunded',
                    ])
                    ->placeholder('All payments'),
                Select::make('payment_method')
                    ->options([
                        'cod'    => 'Cash on Delivery',
                        'bkash'  => 'bKash',
                        'online' => 'Online',
                    ])
                    ->placeholder('All methods'),
            ])
            ->statePath('data')
            ->columns(['default' => 1, 'md' => 3, 'xl' => 5]);
    }

    public function getReport(): array
    {
        $from = Carbon::parse($this->data['from'] ?? Carbon::now()->subMonth())->startOfDay();
        $to   = Carbon::parse($this->data['to']   ?? Carbon::now())->endOfDay();

        $query = Order::query()->whereBetween('created_at', [$from, $to]);

        if (!empty($this->data['status']))          $query->where('status', $this->data['status']);
        if (!empty($this->data['payment_status']))  $query->where('payment_status', $this->data['payment_status']);
        if (!empty($this->data['payment_method']))  $query->where('payment_method', $this->data['payment_method']);

        $orders = (clone $query)->orderByDesc('created_at')->limit(500)->get();

        return [
            'orders'        => $orders,
            'totalOrders'   => (clone $query)->count(),
            'totalRevenue'  => (float) (clone $query)->where('payment_status', 'paid')->sum('total'),
            'pendingCount'  => (clone $query)->where('status', 'pending')->count(),
            'avgOrder'      => (float) (clone $query)->where('payment_status', 'paid')->avg('total') ?: 0,
        ];
    }
}
