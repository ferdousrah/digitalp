<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesWithPermission;
use App\Filament\Concerns\ExportsToCsv;
use App\Filament\Resources\SupplierResource\Pages;
use App\Models\Supplier;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class SupplierResource extends Resource
{
    use AuthorizesWithPermission;
    use ExportsToCsv;
    protected static ?string $permissionKey = 'suppliers';

    protected static ?string $model = Supplier::class;
    protected static ?string $recordTitleAttribute = 'name';
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Inventory';
    protected static ?string $navigationLabel = 'Suppliers';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->columns(2)->schema([
                Forms\Components\TextInput::make('name')->required()->maxLength(255)->columnSpanFull(),
                Forms\Components\TextInput::make('contact_name')->label('Contact Person'),
                Forms\Components\TextInput::make('phone')->tel(),
                Forms\Components\TextInput::make('email')->email(),
                Forms\Components\Toggle::make('is_active')->default(true),
                Forms\Components\Textarea::make('address')->rows(2)->columnSpanFull(),
                Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => Pages\EditSupplier::getUrl(['record' => $record]))
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable()->weight('semibold'),
                Tables\Columns\TextColumn::make('contact_name')->label('Contact')->searchable(),
                Tables\Columns\TextColumn::make('phone')->searchable(),
                Tables\Columns\TextColumn::make('email')->searchable()->toggleable(),
                Tables\Columns\TextColumn::make('purchase_orders_count')->counts('purchaseOrders')->label('POs')->badge(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->actions([Tables\Actions\EditAction::make()])
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
            'index'  => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit'   => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
