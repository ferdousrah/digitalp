<?php

namespace App\Filament\Pages\Reports;

use App\Filament\Pages\Reports\Concerns\AuthorizesReport;
use Filament\Pages\Page;

class ReportsIndex extends Page
{
    use AuthorizesReport;

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationLabel = 'All Reports';
    protected static ?string $navigationGroup = 'Reports';
    protected static ?string $title = 'Reports';
    protected static ?int $navigationSort = 1;
    protected static string $routePath = 'reports';
    protected static ?string $slug = 'reports';
    protected static string $view = 'filament.pages.reports.index';

    public function getSubheading(): ?string
    {
        return 'Analytical breakdowns of your sales, expenses, inventory, and customers.';
    }

    public function getReports(): array
    {
        return [
            [
                'label'       => 'Sales Report',
                'description' => 'Orders with filters by date, status, and payment method.',
                'icon'        => 'heroicon-o-shopping-bag',
                'color'       => '#3b82f6',
                'href'        => SalesReport::getUrl(),
            ],
            [
                'label'       => 'Inventory Report',
                'description' => 'Stock levels, inventory value, and low-stock alerts.',
                'icon'        => 'heroicon-o-archive-box',
                'color'       => '#f59e0b',
                'href'        => InventoryReport::getUrl(),
            ],
            [
                'label'       => 'Customer Report',
                'description' => 'Top customers by total spending and order count.',
                'icon'        => 'heroicon-o-users',
                'color'       => '#8b5cf6',
                'href'        => CustomerReport::getUrl(),
            ],
        ];
    }

    /** Accounts reports — shown separately on this page, navigation lives under Accounts group. */
    public function getAccountsReports(): array
    {
        return [
            [
                'label'       => 'Profit & Loss',
                'description' => 'Monthly revenue, expenses, and net profit over time.',
                'icon'        => 'heroicon-o-scale',
                'color'       => '#10b981',
                'href'        => ProfitLossReport::getUrl(),
            ],
            [
                'label'       => 'Expense Report',
                'description' => 'All expenses grouped by category over a time range.',
                'icon'        => 'heroicon-o-document-chart-bar',
                'color'       => '#ef4444',
                'href'        => ExpenseReport::getUrl(),
            ],
        ];
    }
}
