<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SiteContentResource\Pages;
use App\Models\SiteContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Concerns\AuthorizesWithPermission;
class SiteContentResource extends Resource
{
    use AuthorizesWithPermission;
    protected static ?string $permissionKey = 'site_contents';

    protected static ?string $model = SiteContent::class;

    protected static ?string $navigationIcon  = 'heroicon-o-language';
    protected static ?string $navigationLabel = 'Site Contents';
    protected static ?string $navigationGroup = 'Settings';
    protected static ?int    $navigationSort  = 10;
    protected static ?string $modelLabel      = 'Content Item';
    protected static ?string $pluralModelLabel = 'Site Contents';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('page')
                    ->label('Page / Section')
                    ->options(static::pageOptions())
                    ->required()
                    ->searchable(),

                Forms\Components\TextInput::make('key')
                    ->label('Key')
                    ->required()
                    ->maxLength(100)
                    ->helperText('Unique identifier (e.g. hero_title). No spaces.')
                    ->rules(['alpha_dash'])
                    ->disabledOn('edit'),

                Forms\Components\TextInput::make('label')
                    ->label('Label')
                    ->required()
                    ->maxLength(255)
                    ->helperText('Human-readable description shown in admin.'),

                Forms\Components\TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->default(0),

                Forms\Components\Textarea::make('value_en')
                    ->label('English Content')
                    ->rows(3)
                    ->columnSpanFull(),

                Forms\Components\Textarea::make('value_bn')
                    ->label('Bengali Content (বাংলা)')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('page')
                    ->label('Page')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'navbar'    => 'info',
                        'footer'    => 'gray',
                        'home'      => 'success',
                        'products'  => 'warning',
                        'cart'      => 'danger',
                        'checkout'  => 'primary',
                        'contact'   => 'info',
                        default     => 'gray',
                    })
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('key')
                    ->label('Key')
                    ->fontFamily('mono')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('label')
                    ->label('Label')
                    ->searchable(),

                Tables\Columns\TextColumn::make('value_en')
                    ->label('English')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->value_en),

                Tables\Columns\TextColumn::make('value_bn')
                    ->label('Bengali')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->value_bn),
            ])
            ->defaultSort('sort_order')
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->orderBy('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSiteContents::route('/'),
            'create' => Pages\CreateSiteContent::route('/create'),
            'edit'   => Pages\EditSiteContent::route('/{record}/edit'),
        ];
    }

    private static function pageOptions(): array
    {
        return [
            'navbar'   => 'Navbar',
            'footer'   => 'Footer',
            'home'     => 'Home Page',
            'products' => 'Products',
            'cart'     => 'Cart',
            'checkout' => 'Checkout',
            'contact'  => 'Contact',
            'common'   => 'Common / Global',
        ];
    }

    protected static function afterSave(): void
    {
        SiteContent::clearCache();
    }
}
