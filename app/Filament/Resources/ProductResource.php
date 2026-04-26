<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Models\Product;
use App\Models\ProductAttribute;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

use App\Filament\Concerns\AuthorizesWithPermission;
use App\Filament\Concerns\ExportsToCsv;

class ProductResource extends Resource
{
    use AuthorizesWithPermission;
    use ExportsToCsv;
    protected static ?string $permissionKey = 'products';

    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-cube';
    protected static ?string $navigationGroup = 'Catalog';
    protected static ?int $navigationSort = 1;
    protected static ?string $recordTitleAttribute = 'name';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'sku'];
    }

    public static function getGlobalSearchResultDetails($record): array
    {
        return array_filter([
            'SKU'   => $record->sku,
            'Brand' => $record->brand?->name,
        ]);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Product')
                ->tabs([
                    Forms\Components\Tabs\Tab::make('General')->schema([
                        Forms\Components\TextInput::make('name')->required()->maxLength(255),
                        Forms\Components\TextInput::make('slug')->maxLength(255),
                        Forms\Components\TextInput::make('sku')->label('SKU')->maxLength(255),
                        Forms\Components\Select::make('brand_id')->relationship('brand', 'name')->searchable()->preload()->nullable(),
                        Forms\Components\Select::make('categories')->relationship('categories', 'name')->multiple()->searchable()->preload(),
                        Forms\Components\RichEditor::make('short_description'),
                        Forms\Components\RichEditor::make('description'),
                    ]),
                    Forms\Components\Tabs\Tab::make('Pricing')->schema([
                        Forms\Components\TextInput::make('price')->numeric()->prefix('Tk'),
                        Forms\Components\TextInput::make('compare_price')->numeric()->prefix('Tk'),
                        Forms\Components\TextInput::make('cost_price')->numeric()->prefix('Tk'),
                        Forms\Components\Toggle::make('in_stock')->default(true),
                        Forms\Components\TextInput::make('stock_quantity')->numeric()->default(0)->label('Stock Quantity'),
                        Forms\Components\TextInput::make('min_stock_quantity')->numeric()->default(5)->label('Min Stock (Low Stock Alert)')->helperText('Alert shows when stock falls at or below this number'),
                    ]),
                    Forms\Components\Tabs\Tab::make('Media')->schema([
                        Forms\Components\Actions::make([
                            \App\Filament\Forms\Actions\PickFromLibraryAction::make('product_thumbnail', 'Pick thumbnail from Library'),
                        ]),
                        SpatieMediaLibraryFileUpload::make('product_thumbnail')
                            ->collection('product_thumbnail')
                            ->image()
                            ->helperText('Shown on product cards and as the poster for the thumbnail video below.'),
                        Forms\Components\Section::make('Thumbnail Video (optional)')
                            ->description('If set, the product card will show a video instead of a static image. The thumbnail image above is used as the poster.')
                            ->collapsed(fn ($record) => empty($record?->thumbnail_video))
                            ->schema([
                                Forms\Components\Select::make('thumbnail_video.type')
                                    ->label('Video Type')
                                    ->options([
                                        ''             => '— None (use image only) —',
                                        'youtube'      => 'YouTube Video',
                                        'youtube_reel' => 'YouTube Reel / Shorts',
                                        'upload'       => 'Upload File',
                                    ])
                                    ->live()
                                    ->default(''),
                                Forms\Components\TextInput::make('thumbnail_video.url')
                                    ->label('YouTube URL')
                                    ->placeholder('https://youtube.com/watch?v=... or https://youtube.com/shorts/...')
                                    ->url()
                                    ->visible(fn (Forms\Get $get) => in_array($get('thumbnail_video.type'), ['youtube', 'youtube_reel'])),
                                Forms\Components\FileUpload::make('thumbnail_video.file')
                                    ->label('Video File')
                                    ->disk('public')
                                    ->directory('products/thumbnail-videos')
                                    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'])
                                    ->maxSize(51200)
                                    ->visible(fn (Forms\Get $get) => $get('thumbnail_video.type') === 'upload')
                                    ->helperText('Short silent loop works best (5–10 seconds, muted). Max 50 MB.'),
                            ]),
                        Forms\Components\Actions::make([
                            \App\Filament\Forms\Actions\PickFromLibraryAction::make('product_images', 'Add gallery image from Library'),
                        ]),
                        SpatieMediaLibraryFileUpload::make('product_images')->collection('product_images')->multiple()->reorderable()->image(),
                        Forms\Components\Repeater::make('videos')
                            ->label('Product Videos')
                            ->helperText('Add YouTube videos, YouTube Shorts/Reels, or upload video files. These appear in the product gallery.')
                            ->schema([
                                Forms\Components\Select::make('type')
                                    ->label('Video Type')
                                    ->options([
                                        'youtube'      => 'YouTube Video',
                                        'youtube_reel' => 'YouTube Reel / Shorts',
                                        'upload'       => 'Upload File',
                                    ])
                                    ->required()
                                    ->live()
                                    ->default('youtube'),
                                Forms\Components\TextInput::make('url')
                                    ->label('YouTube URL')
                                    ->placeholder('https://youtube.com/watch?v=... or https://youtube.com/shorts/...')
                                    ->url()
                                    ->visible(fn (Forms\Get $get) => in_array($get('type'), ['youtube', 'youtube_reel']))
                                    ->required(fn (Forms\Get $get) => in_array($get('type'), ['youtube', 'youtube_reel'])),
                                Forms\Components\FileUpload::make('file')
                                    ->label('Video File')
                                    ->disk('public')
                                    ->directory('products/videos')
                                    ->acceptedFileTypes(['video/mp4', 'video/webm', 'video/ogg', 'video/quicktime'])
                                    ->maxSize(51200) // 50 MB
                                    ->visible(fn (Forms\Get $get) => $get('type') === 'upload')
                                    ->required(fn (Forms\Get $get) => $get('type') === 'upload'),
                                Forms\Components\TextInput::make('title')
                                    ->label('Title (optional)')
                                    ->maxLength(255),
                            ])
                            ->columns(1)
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible()
                            ->addActionLabel('Add Video')
                            ->itemLabel(fn (array $state): ?string =>
                                ($state['title'] ?? null)
                                    ?: (($state['type'] ?? '') === 'upload' ? 'Uploaded video' : ($state['url'] ?? 'New video'))
                            ),
                    ]),
                    Forms\Components\Tabs\Tab::make('SEO')->schema([
                        \App\Filament\Forms\SeoSection::make(),
                    ]),
                    Forms\Components\Tabs\Tab::make('Attributes')->schema([
                        Forms\Components\Repeater::make('attributeValues')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_attribute_id')
                                    ->label('Attribute')
                                    ->options(ProductAttribute::orderBy('sort_order')->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),
                                Forms\Components\TextInput::make('value')
                                    ->required()
                                    ->maxLength(255),
                            ])
                            ->columns(2)
                            ->defaultItems(0)
                            ->addActionLabel('Add Attribute Value')
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string =>
                                ($state['product_attribute_id'] ?? null)
                                    ? (ProductAttribute::find($state['product_attribute_id'])?->name . ': ' . ($state['value'] ?? ''))
                                    : null
                            ),
                    ]),
                    Forms\Components\Tabs\Tab::make('Details')->schema([
                        Forms\Components\KeyValue::make('specifications'),
                        Forms\Components\TextInput::make('weight')->maxLength(255),
                        Forms\Components\TextInput::make('dimensions')->maxLength(255),
                        Forms\Components\TextInput::make('warranty_info')->maxLength(255),
                    ]),
                ])->columnSpanFull(),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\Toggle::make('is_featured')->default(false),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => Pages\EditProduct::getUrl(['record' => $record]))
            ->columns([
                SpatieMediaLibraryImageColumn::make('product_thumbnail')->collection('product_thumbnail')->circular(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('sku')->label('SKU')->searchable(),
                Tables\Columns\TextColumn::make('price')->money('BDT')->sortable(),
                Tables\Columns\TextColumn::make('brand.name')->sortable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])->filters([
                Tables\Filters\TernaryFilter::make('is_active'),
                Tables\Filters\TernaryFilter::make('is_featured'),
                Tables\Filters\SelectFilter::make('brand')->relationship('brand', 'name'),
            ])->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('adjustStock')
                    ->label('Adjust Stock')
                    ->icon('heroicon-o-adjustments-horizontal')
                    ->color('warning')
                    ->form([
                        Forms\Components\Placeholder::make('current_stock')
                            ->content(fn ($record) => "Current stock: " . ($record->stock_quantity ?? 0)),
                        Forms\Components\TextInput::make('delta')
                            ->label('Change by')
                            ->numeric()
                            ->required()
                            ->helperText('Use a positive number to add, negative to remove.'),
                        Forms\Components\Textarea::make('reason')
                            ->label('Reason')
                            ->rows(2)
                            ->required()
                            ->placeholder('e.g. Damaged units removed, stock-take correction'),
                    ])
                    ->action(function ($record, array $data) {
                        \App\Services\InventoryService::move(
                            product: $record,
                            delta: (int) $data['delta'],
                            type: 'adjustment',
                            reference: null,
                            notes: $data['reason'],
                        );
                    }),
            ])->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    static::csvExportBulkAction(),
                ]),
            ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
