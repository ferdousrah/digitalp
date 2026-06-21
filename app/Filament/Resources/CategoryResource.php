<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

use App\Filament\Concerns\AuthorizesWithPermission;
class CategoryResource extends Resource
{
    use AuthorizesWithPermission;
    protected static ?string $permissionKey = 'categories';

    protected static ?string $model = Category::class;
    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Category')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('General')
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->rows(3),
                                Forms\Components\Select::make('parent_id')
                                    ->label('Parent Category')
                                    ->relationship(
                                        name: 'parent',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn (Builder $query, ?Category $record) =>
                                            $query->when($record, fn ($q) => $q->whereKeyNot($record->getKey()))
                                                  ->orderBy('name'),
                                    )
                                    ->getOptionLabelFromRecordUsing(fn (Category $record) => static::categoryPath($record))
                                    ->searchable()
                                    ->preload()
                                    ->nullable()
                                    ->helperText('Leave empty for a top-level category. Nesting supports up to 3 levels (Parent › Child › Sub-child).'),
                                Forms\Components\Toggle::make('is_active')
                                    ->default(true),
                                Forms\Components\TextInput::make('sort_order')
                                    ->numeric()
                                    ->default(0),
                                Forms\Components\Actions::make([
                                    \App\Filament\Forms\Actions\PickFromLibraryAction::make('category_image', 'Pick image from Library'),
                                ]),
                                SpatieMediaLibraryFileUpload::make('category_image')
                                    ->collection('category_image')
                                    ->image(),
                                SpatieMediaLibraryFileUpload::make('category_banner')
                                    ->collection('category_banner')
                                    ->label('Section Banner (mobile)')
                                    ->helperText('Wide banner shown in the mobile Categories page section header (parent categories). Recommended ~600×300.')
                                    ->image()
                                    ->imageEditor(),
                            ]),
                        Forms\Components\Tabs\Tab::make('Filter Attributes')
                            ->schema([
                                Forms\Components\Select::make('filterAttributes')
                                    ->relationship('filterAttributes', 'name')
                                    ->multiple()
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Select which product attributes appear as filters when browsing this category'),
                            ]),
                        Forms\Components\Tabs\Tab::make('SEO')
                            ->schema([
                                \App\Filament\Forms\SeoSection::make(),
                            ]),
                        Forms\Components\Tabs\Tab::make('SEO Content & FAQ')
                            ->schema([
                                Forms\Components\RichEditor::make('seo_content')
                                    ->label('Long-form SEO content')
                                    ->helperText('Rich content (buying guide / overview) shown below the products on the category page. Great for ranking and AI Overview — aim for a few hundred to a few thousand words.')
                                    ->columnSpanFull(),
                                Forms\Components\Repeater::make('faqs')
                                    ->label('Category FAQs')
                                    ->helperText('Shown on the category page and emitted as FAQ structured data — helps the page answer user questions in Google AI Overview and ChatGPT/Gemini.')
                                    ->schema([
                                        Forms\Components\TextInput::make('question')->required()->maxLength(255),
                                        Forms\Components\Textarea::make('answer')->required()->rows(3),
                                    ])
                                    ->itemLabel(fn (array $state): ?string => $state['question'] ?? 'Question')
                                    ->collapsible()
                                    ->reorderable()
                                    ->defaultItems(0)
                                    ->addActionLabel('Add FAQ'),
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug'),
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Parent'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }

    /**
     * Build a breadcrumb-style hierarchy path (e.g. "Skin Care › Cleansers › Toners")
     * so the Parent Category dropdown is unambiguous across 3 nesting levels.
     * A single cached map keeps this O(1) per option (no N+1 on preload).
     */
    protected static function categoryPath(Category $record): string
    {
        static $map = null;
        if ($map === null) {
            $map = Category::query()->get(['id', 'name', 'parent_id'])->keyBy('id');
        }

        $parts = [];
        $node  = $map->get($record->getKey()) ?? $record;
        $guard = 0;
        while ($node && $guard++ < 6) {
            array_unshift($parts, $node->name);
            $node = $node->parent_id ? $map->get($node->parent_id) : null;
        }

        return implode(' › ', $parts);
    }
}