<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Pages\Reports\Concerns\AuthorizesReport;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseReport extends Page implements HasForms
{
    use AuthorizesReport;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Expense Report';
    protected static ?string $navigationGroup = 'Accounts';
    protected static ?string $title = 'Expense Report';
    protected static ?int $navigationSort = 5;
    protected static string $routePath = 'reports/expenses';
    protected static ?string $slug = 'reports/expenses';
    protected static string $view = 'filament.pages.reports.expenses';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'from' => Carbon::now()->subMonths(2)->startOfMonth()->toDateString(),
            'to'   => Carbon::now()->endOfMonth()->toDateString(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('from')->native(false)->required(),
                DatePicker::make('to')->native(false)->required(),
                Select::make('expense_category_id')
                    ->label('Category')
                    ->options(ExpenseCategory::orderBy('name')->pluck('name', 'id'))
                    ->placeholder('All categories')
                    ->searchable(),
            ])
            ->statePath('data')
            ->columns(['default' => 1, 'md' => 3]);
    }

    public function getReport(): array
    {
        $from = Carbon::parse($this->data['from'] ?? Carbon::now()->subMonth())->startOfDay();
        $to   = Carbon::parse($this->data['to']   ?? Carbon::now())->endOfDay();

        $baseQuery = Expense::query()->whereBetween('expense_date', [$from, $to]);
        if (!empty($this->data['expense_category_id'])) {
            $baseQuery->where('expense_category_id', $this->data['expense_category_id']);
        }

        $expenses = (clone $baseQuery)->with('category')
            ->orderByDesc('expense_date')
            ->orderByDesc('amount')
            ->limit(500)
            ->get();

        // By-category aggregate
        $byCategory = DB::table('expenses')
            ->leftJoin('expense_categories', 'expense_categories.id', '=', 'expenses.expense_category_id')
            ->selectRaw('expense_categories.name as name, expense_categories.color as color, SUM(expenses.amount) as total, COUNT(*) as count')
            ->whereBetween('expenses.expense_date', [$from, $to])
            ->when(!empty($this->data['expense_category_id']), fn ($q) => $q->where('expenses.expense_category_id', $this->data['expense_category_id']))
            ->groupBy('expense_categories.id', 'expense_categories.name', 'expense_categories.color')
            ->orderByDesc('total')
            ->get();

        $total = (float) (clone $baseQuery)->sum('amount');

        return [
            'expenses'   => $expenses,
            'byCategory' => $byCategory,
            'total'      => $total,
            'count'      => (clone $baseQuery)->count(),
            'avg'        => (float) (clone $baseQuery)->avg('amount') ?: 0,
        ];
    }
}
