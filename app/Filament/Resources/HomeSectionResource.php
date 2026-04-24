<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HomeSectionResource\Pages;
use App\Models\Category;
use App\Models\HomeSection;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HomeSectionResource extends Resource
{
    protected static ?string $model = HomeSection::class;
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Homepage Sections';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make('Tabs')->tabs([

                // ── Tab 1: General ──────────────────────────────────────
                Forms\Components\Tabs\Tab::make('General')->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('subtitle')
                        ->maxLength(500),
                    Forms\Components\Toggle::make('is_enabled')
                        ->default(true),
                    Forms\Components\TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                    Forms\Components\ColorPicker::make('bg_color')
                        ->label('Background Color')
                        ->default('#ffffff'),
                    Forms\Components\TextInput::make('padding_y')
                        ->numeric()
                        ->label('Section vertical padding')
                        ->suffix('px')
                        ->default(48),
                ]),

                // ── Tab 2: Heading & Typography ─────────────────────────
                Forms\Components\Tabs\Tab::make('Heading & Typography')->schema([
                    Forms\Components\Section::make('Heading')->schema([
                        Forms\Components\ColorPicker::make('heading_color')
                            ->default('#111827'),
                        Forms\Components\TextInput::make('heading_size_desktop')
                            ->label('Size — Desktop')
                            ->placeholder('28px'),
                        Forms\Components\TextInput::make('heading_size_mobile')
                            ->label('Size — Mobile')
                            ->placeholder('22px'),
                        Forms\Components\Select::make('heading_weight')
                            ->options([
                                '300' => 'Light 300',
                                '400' => 'Regular 400',
                                '500' => 'Medium 500',
                                '600' => 'SemiBold 600',
                                '700' => 'Bold 700',
                                '800' => 'ExtraBold 800',
                            ])
                            ->default('700'),
                    ])->columns(4),

                    Forms\Components\Section::make('Sub-Heading')->schema([
                        Forms\Components\ColorPicker::make('subheading_color')
                            ->default('#6b7280'),
                        Forms\Components\TextInput::make('subheading_size_desktop')
                            ->label('Size — Desktop')
                            ->placeholder('16px'),
                        Forms\Components\TextInput::make('subheading_size_mobile')
                            ->label('Size — Mobile')
                            ->placeholder('14px'),
                        Forms\Components\Select::make('subheading_weight')
                            ->options([
                                '300' => 'Light 300',
                                '400' => 'Regular 400',
                                '500' => 'Medium 500',
                                '600' => 'SemiBold 600',
                                '700' => 'Bold 700',
                                '800' => 'ExtraBold 800',
                            ])
                            ->default('400'),
                    ])->columns(4),

                    Forms\Components\Section::make('Alignment')->schema([
                        Forms\Components\Select::make('extra.heading_align')
                            ->label('Heading Alignment')
                            ->options([
                                'left'   => 'Left',
                                'center' => 'Center',
                                'right'  => 'Right',
                            ])
                            ->default('left'),
                        Forms\Components\Select::make('extra.content_align')
                            ->label('Content Alignment')
                            ->options([
                                'left'   => 'Left',
                                'center' => 'Center',
                                'right'  => 'Right',
                            ])
                            ->default('left'),
                    ])->columns(2),

                    Forms\Components\Section::make('Divider')->schema([
                        Forms\Components\Toggle::make('extra.show_divider')
                            ->label('Show Divider')
                            ->default(true),
                        Forms\Components\ColorPicker::make('extra.divider_color')
                            ->label('Divider Color')
                            ->helperText('Leave empty to use the primary brand color.'),
                    ])->columns(2),
                ]),

                // ── Tab 3: Display ──────────────────────────────────────
                Forms\Components\Tabs\Tab::make('Display')
                    ->visible(fn (Forms\Get $get) => in_array($get('type'), ['product', 'category', 'blog', 'brands']))
                    ->schema([
                        Forms\Components\Select::make('display_type')
                            ->options([
                                'grid'     => 'Grid',
                                'carousel' => 'Carousel',
                            ])
                            ->default('grid')
                            ->live(),

                        Forms\Components\Section::make('Grid Settings')
                            ->visible(fn (Forms\Get $get) => $get('display_type') === 'grid')
                            ->schema([
                                Forms\Components\Select::make('desktop_columns')
                                    ->label('Desktop Columns')
                                    ->options([
                                        '2' => '2 cols',
                                        '3' => '3 cols',
                                        '4' => '4 cols',
                                        '5' => '5 cols',
                                        '6' => '6 cols',
                                    ])
                                    ->default('4'),
                                Forms\Components\Select::make('mobile_columns')
                                    ->label('Mobile Columns')
                                    ->options([
                                        '1' => '1 col',
                                        '2' => '2 cols',
                                        '3' => '3 cols',
                                    ])
                                    ->default('2'),
                                Forms\Components\TextInput::make('rows')
                                    ->numeric()
                                    ->label('Rows to show')
                                    ->helperText('Number of rows × desktop columns = max items shown')
                                    ->default(2),
                            ])->columns(3),

                        Forms\Components\Section::make('Carousel Settings')
                            ->visible(fn (Forms\Get $get) => $get('display_type') === 'carousel')
                            ->schema([
                                Forms\Components\TextInput::make('desktop_visible')
                                    ->numeric()
                                    ->label('Visible items — Desktop')
                                    ->default(4),
                                Forms\Components\TextInput::make('mobile_visible')
                                    ->numeric()
                                    ->label('Visible items — Mobile')
                                    ->default(2),
                                Forms\Components\TextInput::make('rows')
                                    ->numeric()
                                    ->label('Rows')
                                    ->helperText('For multi-row carousels (usually 1)')
                                    ->default(1),
                            ])->columns(3),

                        Forms\Components\TextInput::make('items_count')
                            ->numeric()
                            ->label('Total products to load')
                            ->default(8),
                    ]),

                // ── Tab 4: Advanced ─────────────────────────────────────
                Forms\Components\Tabs\Tab::make('Advanced')->schema([

                    // product type fields
                    Forms\Components\Select::make('extra.product_filter')
                        ->label('Product Filter')
                        ->options([
                            'featured'    => 'Featured',
                            'new_arrival' => 'New Arrival',
                            'best_seller' => 'Best Seller',
                            'category'    => 'By Category',
                        ])
                        ->live()
                        ->visible(fn (Forms\Get $get) => $get('type') === 'product'),

                    Forms\Components\Select::make('extra.category_id')
                        ->label('Category')
                        ->options(fn () => Category::whereNull('parent_id')->pluck('name', 'id')->toArray())
                        ->visible(fn (Forms\Get $get) => $get('type') === 'product' && $get('extra.product_filter') === 'category'),

                    // offer_banner type fields
                    Forms\Components\Select::make('extra.columns')
                        ->label('Banner columns')
                        ->options([
                            '1' => '1 Column (full width)',
                            '2' => '2 Columns',
                        ])
                        ->visible(fn (Forms\Get $get) => $get('type') === 'offer_banner'),

                    // seo type fields
                    \FilamentTiptapEditor\TiptapEditor::make('extra.content')
                        ->label('SEO Content')
                        ->tools([
                            'bold', 'italic', 'underline', 'strike',
                            '|',
                            'heading', 'lead', 'small',
                            '|',
                            'color', 'highlight',
                            '|',
                            'align-left', 'align-center', 'align-right', 'align-justify',
                            '|',
                            'bullet-list', 'ordered-list', 'blockquote',
                            '|',
                            'link', 'media',
                            '|',
                            'code', 'code-block',
                            '|',
                            'undo', 'redo',
                        ])
                        ->output(\FilamentTiptapEditor\Enums\TiptapOutput::Html)
                        ->visible(fn (Forms\Get $get) => $get('type') === 'seo'),
                ]),

            ])->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->defaultSort('sort_order')
            ->paginated(false)
            ->columns([
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('type'),
                Tables\Columns\ToggleColumn::make('is_enabled')
                    ->label('Enabled'),
                Tables\Columns\TextColumn::make('display_type')
                    ->label('Display'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHomeSections::route('/'),
            'edit'  => Pages\EditHomeSection::route('/{record}/edit'),
        ];
    }
}
