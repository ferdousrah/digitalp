<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductAttributeResource\Pages;
use App\Models\ProductAttribute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

use App\Filament\Concerns\AuthorizesWithPermission;
class ProductAttributeResource extends Resource
{
    use AuthorizesWithPermission;
    protected static ?string $permissionKey = 'attributes';

    protected static ?string $model = ProductAttribute::class;
    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';
    protected static ?string $navigationGroup = 'Catalog';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationLabel = 'Attributes';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')
                ->required()
                ->maxLength(255),
            Forms\Components\TextInput::make('slug')
                ->maxLength(255)
                ->hint('Auto-generated from name'),
            Forms\Components\Select::make('type')
                ->options([
                    'text' => 'Text',
                    'number' => 'Number',
                    'select' => 'Select',
                ])
                ->default('text'),
            Forms\Components\Toggle::make('is_filterable')
                ->label('Show in filters')
                ->default(true)
                ->helperText('Enable to show this attribute in the frontend filter sidebar'),
            Forms\Components\TextInput::make('sort_order')
                ->numeric()
                ->default(0),
            Forms\Components\Select::make('categories')
                ->relationship('categories', 'name')
                ->multiple()
                ->searchable()
                ->preload()
                ->helperText('Assign to categories where this attribute appears as a filter'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('type')->badge(),
                Tables\Columns\IconColumn::make('is_filterable')->boolean()->label('Filterable'),
                Tables\Columns\TextColumn::make('categories_count')
                    ->counts('categories')
                    ->label('Categories'),
                Tables\Columns\TextColumn::make('values_count')
                    ->counts('values')
                    ->label('Values'),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_filterable'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductAttributes::route('/'),
            'create' => Pages\CreateProductAttribute::route('/create'),
            'edit' => Pages\EditProductAttribute::route('/{record}/edit'),
        ];
    }
}
