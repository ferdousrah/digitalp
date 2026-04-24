<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class AccountingDashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationLabel = 'Accounting Dashboard';
    protected static ?string $navigationGroup = 'Accounts';
    protected static ?string $title = 'Accounting Overview';
    protected static ?int $navigationSort = 1;
    protected static string $routePath = 'accounting-dashboard';
    protected static ?string $slug = 'accounting';

    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->can('expenses.view'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public function getSubheading(): ?string
    {
        return 'Expenses, revenue, and profit — at a glance.';
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Widgets\AccountingStatsWidget::class,
            \App\Filament\Widgets\ExpenseByCategoryWidget::class,
            \App\Filament\Widgets\MonthlyExpenseWidget::class,
            \App\Filament\Widgets\RecentExpensesWidget::class,
        ];
    }

    public function getColumns(): int | array
    {
        return ['sm' => 1, 'md' => 2, 'xl' => 4];
    }
}
