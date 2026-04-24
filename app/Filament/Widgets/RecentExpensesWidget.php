<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ExpenseResource;
use App\Models\Expense;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentExpensesWidget extends BaseWidget
{
    protected static ?string $heading = 'Recent Expenses';
    protected static ?int $sort = 4;
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(Expense::query()->with('category')->latest('expense_date')->limit(10))
            ->recordUrl(fn ($record) => ExpenseResource::getUrl('edit', ['record' => $record]))
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('expense_date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->weight('semibold')
                    ->searchable()
                    ->limit(40),
                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state) return '—';
                        $color = $record->category->color ?? '#64748b';
                        return new \Illuminate\Support\HtmlString(
                            '<span style="display:inline-flex; align-items:center; gap:6px;">
                                <span style="width:8px; height:8px; border-radius:50%; background:' . e($color) . ';"></span>
                                ' . e($state) . '
                            </span>'
                        );
                    }),
                Tables\Columns\TextColumn::make('amount')
                    ->money('BDT')
                    ->weight('bold')
                    ->color('danger'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->formatStateUsing(fn ($state) => Expense::paymentMethods()[$state] ?? $state)
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('paid_to')->limit(25)->toggleable(),
            ]);
    }
}
