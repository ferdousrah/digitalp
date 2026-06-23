<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesWithPermission;
use App\Filament\Resources\OfferResource\Pages;
use App\Models\Offer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class OfferResource extends Resource
{
    use AuthorizesWithPermission;
    protected static ?string $permissionKey = 'offers';

    protected static ?string $model = Offer::class;
    protected static ?string $navigationIcon  = 'heroicon-o-gift';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int    $navigationSort  = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Tabs::make()->columnSpanFull()->tabs([

                Forms\Components\Tabs\Tab::make('Offer')->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()->maxLength(255)->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, Forms\Set $set, ?Offer $record) =>
                            $record ? null : $set('slug', \Illuminate\Support\Str::slug($state))),
                    Forms\Components\TextInput::make('slug')
                        ->maxLength(255)
                        ->helperText('Leave blank to auto-generate from the title.'),
                    Forms\Components\TextInput::make('subtitle')
                        ->label('Tagline')->maxLength(255)
                        ->placeholder('Easy installments with zero interest'),
                    SpatieMediaLibraryFileUpload::make('offer_banner')
                        ->collection('offer_banner')->label('Banner image')
                        ->image()->imageEditor()
                        ->helperText('Wide promo banner shown on the card + detail page. Recommended ~1400×700.'),
                    Forms\Components\RichEditor::make('body')
                        ->label('Details / Terms')->columnSpanFull()
                        ->helperText('Full promotion details, terms & conditions, product links, etc.'),
                ])->columns(2),

                Forms\Components\Tabs\Tab::make('Schedule & CTA')->schema([
                    Forms\Components\DateTimePicker::make('starts_at')
                        ->label('Starts at')->seconds(false)->native(false),
                    Forms\Components\DateTimePicker::make('ends_at')
                        ->label('Ends at')->seconds(false)->native(false)
                        ->helperText('Drives the "ending in" countdown on the detail page.'),
                    Forms\Components\TextInput::make('cta_label')
                        ->label('Button label')->maxLength(40)->placeholder('Shop Now'),
                    Forms\Components\TextInput::make('cta_url')
                        ->label('Button link')->maxLength(255)->placeholder('/products?sale=1'),
                    Forms\Components\Toggle::make('is_active')->default(true),
                    Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                ])->columns(2),

                Forms\Components\Tabs\Tab::make('SEO')->schema([
                    Forms\Components\TextInput::make('meta_title')->maxLength(255),
                    Forms\Components\Textarea::make('meta_description')->rows(3)->maxLength(300),
                ]),

            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('sort_order')->columns([
            SpatieMediaLibraryImageColumn::make('offer_banner')->collection('offer_banner')->label('Banner'),
            Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('starts_at')->date()->sortable()->toggleable(),
            Tables\Columns\TextColumn::make('ends_at')->date()->sortable(),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
            Tables\Columns\TextColumn::make('sort_order')->sortable(),
        ])->filters([
            Tables\Filters\TernaryFilter::make('is_active'),
        ])->actions([
            Tables\Actions\EditAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
        ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListOffers::route('/'),
            'create' => Pages\CreateOffer::route('/create'),
            'edit'   => Pages\EditOffer::route('/{record}/edit'),
        ];
    }
}
