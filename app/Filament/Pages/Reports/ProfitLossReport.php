<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Pages\Reports\Concerns\AuthorizesReport;
use App\Models\Expense;
use App\Models\Order;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;

class ProfitLossReport extends Page implements HasForms
{
    use AuthorizesReport;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-scale';
    protected static ?string $navigationLabel = 'Profit & Loss';
    protected static ?string $navigationGroup = 'Accounts';
    protected static ?string $title = 'Profit & Loss';
    protected static ?int $navigationSort = 4;
    protected static string $routePath = 'reports/profit-loss';
    protected static ?string $slug = 'reports/profit-loss';
    protected static string $view = 'filament.pages.reports.profit-loss';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'from' => Carbon::now()->subMonths(11)->startOfMonth()->toDateString(),
            'to'   => Carbon::now()->endOfMonth()->toDateString(),
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

    public function getMonthlyBreakdown(): array
    {
        $from = Carbon::parse($this->data['from'] ?? Carbon::now()->subYear())->startOfMonth();
        $to   = Carbon::parse($this->data['to']   ?? Carbon::now())->endOfMonth();

        $rows = [];
        $cursor = $from->copy();

        $totalRevenue = 0.0;
        $totalExpense = 0.0;

        while ($cursor <= $to) {
            $monthStart = $cursor->copy()->startOfMonth();
            $monthEnd   = $cursor->copy()->endOfMonth();

            $rev = (float) Order::where('payment_status', 'paid')
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total');

            $exp = (float) Expense::whereBetween('expense_date', [$monthStart, $monthEnd])
                ->sum('amount');

            $rows[] = [
                'label'    => $cursor->format('M Y'),
                'revenue'  => $rev,
                'expense'  => $exp,
                'profit'   => $rev - $exp,
                'margin'   => $rev > 0 ? round((($rev - $exp) / $rev) * 100, 1) : null,
            ];

            $totalRevenue += $rev;
            $totalExpense += $exp;
            $cursor->addMonth();
        }

        return [
            'rows'         => $rows,
            'totalRevenue' => $totalRevenue,
            'totalExpense' => $totalExpense,
            'totalProfit'  => $totalRevenue - $totalExpense,
            'totalMargin'  => $totalRevenue > 0 ? round((($totalRevenue - $totalExpense) / $totalRevenue) * 100, 1) : null,
        ];
    }
}
