<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuItemResource\Pages;
use App\Models\Category;
use App\Models\MenuItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use App\Filament\Concerns\AuthorizesWithPermission;
class MenuItemResource extends Resource
{
    use AuthorizesWithPermission;
    protected static ?string $permissionKey = 'menu_items';

    protected static ?string $model = MenuItem::class;
    protected static ?string $navigationIcon = 'heroicon-o-bars-3';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?string $navigationLabel = 'Menu Items';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('label')
                    ->required()
                    ->maxLength(100)
                    ->columnSpan(2),

                Forms\Components\Select::make('type')
                    ->options(['link' => 'Custom Link', 'category' => 'Category'])
                    ->default('link')
                    ->live()
                    ->required(),

                Forms\Components\TextInput::make('url')
                    ->label('URL')
                    ->placeholder('/products or https://example.com')
                    ->hidden(fn (Get $get) => $get('type') === 'category')
                    ->maxLength(500),

                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->options(Category::whereNull('parent_id')->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->hidden(fn (Get $get) => $get('type') !== 'category'),

                Forms\Components\TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->label('Sort Order'),

                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),

                Forms\Components\Toggle::make('open_in_new_tab')
                    ->label('Open in new tab')
                    ->default(false),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')->label('#')->sortable()->width(50),
                Tables\Columns\TextColumn::make('label')->searchable()->sortable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors(['primary' => 'link', 'success' => 'category']),
                Tables\Columns\TextColumn::make('url')->label('URL / Category')
                    ->formatStateUsing(fn ($state, $record) => $record->type === 'category'
                        ? ($record->category?->name ?? '—')
                        : ($state ?? '—'))
                    ->limit(40),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Active'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()])]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit'   => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
