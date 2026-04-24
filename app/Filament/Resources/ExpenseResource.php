<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesWithPermission;
use App\Filament\Concerns\ExportsToCsv;
use App\Filament\Resources\ExpenseResource\Pages;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExpenseResource extends Resource
{
    use AuthorizesWithPermission;
    use ExportsToCsv;
    protected static ?string $permissionKey = 'expenses';

    protected static ?string $model = Expense::class;
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Accounts';
    protected static ?string $navigationLabel = 'Expenses';
    protected static ?int $navigationSort = 2;

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'reference', 'paid_to'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Expense Details')->columns(2)->schema([
                Forms\Components\Select::make('expense_category_id')
                    ->label('Category')
                    ->options(ExpenseCategory::where('is_active', true)->orderBy('sort_order')->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')->required()->maxLength(255),
                        Forms\Components\ColorPicker::make('color')->default('#64748b'),
                    ])
                    ->createOptionUsing(fn (array $data) => ExpenseCategory::create($data)->getKey()),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('e.g. Office rent March 2026'),
                Forms\Components\TextInput::make('amount')
                    ->numeric()
                    ->required()
                    ->prefix('৳')
                    ->minValue(0),
                Forms\Components\DatePicker::make('expense_date')
                    ->required()
                    ->default(now())
                    ->native(false),
                Forms\Components\Select::make('payment_method')
                    ->options(Expense::paymentMethods())
                    ->default('cash')
                    ->required(),
                Forms\Components\TextInput::make('reference')
                    ->label('Reference / Invoice #')
                    ->maxLength(255),
                Forms\Components\TextInput::make('paid_to')
                    ->label('Paid To (vendor / payee)')
                    ->maxLength(255)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
            ]),

            Forms\Components\Section::make('Recurrence')
                ->collapsed()
                ->columns(2)
                ->schema([
                    Forms\Components\Toggle::make('is_recurring')->default(false)->live(),
                    Forms\Components\Select::make('recurrence_period')
                        ->options(Expense::recurrencePeriods())
                        ->visible(fn (Forms\Get $get) => $get('is_recurring')),
                ]),

            Forms\Components\Section::make('Receipt')
                ->collapsed()
                ->schema([
                    Forms\Components\FileUpload::make('receipt_path')
                        ->label('Receipt / Invoice (optional)')
                        ->disk('public')
                        ->directory('expenses/receipts')
                        ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/webp', 'application/pdf'])
                        ->maxSize(5120),
                ]),
        ]);
    }

    protected static function boot(): void
    {
        // Auto-set created_by on new records
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => Pages\EditExpense::getUrl(['record' => $record]))
            ->defaultSort('expense_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('expense_date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->weight('semibold')
                    ->wrap(),
                Tables\Columns\TextColumn::make('category.name')
                    ->badge()
                    ->color(fn ($state, $record) => $record?->category?->color ? null : 'gray')
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
                    ->sortable()
                    ->weight('bold')
                    ->color('danger'),
                Tables\Columns\TextColumn::make('payment_method')
                    ->formatStateUsing(fn ($state) => Expense::paymentMethods()[$state] ?? $state)
                    ->badge(),
                Tables\Columns\TextColumn::make('paid_to')
                    ->toggleable()
                    ->limit(25),
                Tables\Columns\IconColumn::make('is_recurring')->boolean()->toggleable(),
                Tables\Columns\TextColumn::make('reference')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('expense_category_id')
                    ->label('Category')
                    ->relationship('category', 'name'),
                SelectFilter::make('payment_method')->options(Expense::paymentMethods()),
                Filter::make('month')
                    ->form([
                        Forms\Components\DatePicker::make('from')->native(false),
                        Forms\Components\DatePicker::make('until')->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('expense_date', '>=', $d))
                            ->when($data['until'] ?? null, fn ($q, $d) => $q->whereDate('expense_date', '<=', $d));
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index'  => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit'   => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
