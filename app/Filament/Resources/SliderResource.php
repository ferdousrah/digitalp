<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SliderResource\Pages;
use App\Models\Slider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

use App\Filament\Concerns\AuthorizesWithPermission;
class SliderResource extends Resource
{
    use AuthorizesWithPermission;
    protected static ?string $permissionKey = 'sliders';

    protected static ?string $model = Slider::class;
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Content';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->maxLength(255),
            Forms\Components\Textarea::make('subtitle')->rows(2),
            Forms\Components\TextInput::make('button_text')->maxLength(255),
            Forms\Components\TextInput::make('button_url')->maxLength(255)->placeholder('e.g. /products or https://example.com'),
            Forms\Components\TextInput::make('link_url')->label('Banner Link (whole image clickable)')->maxLength(255)->placeholder('e.g. /products or https://example.com')->helperText('Optional. Makes the entire banner/slide image a clickable link.'),
            Forms\Components\Select::make('position')->options([
                'home_hero'     => 'Home Hero Slider',
                'home_banner_1' => 'Home Banner 1 (Top Right)',
                'home_banner_2' => 'Home Banner 2 (Bottom Right)',
            ]),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            Forms\Components\DateTimePicker::make('starts_at'),
            Forms\Components\DateTimePicker::make('ends_at'),
            SpatieMediaLibraryFileUpload::make('slide_image')->collection('slide_image')->image()->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            SpatieMediaLibraryImageColumn::make('slide_image')->collection('slide_image'),
            Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('position'),
            Tables\Columns\IconColumn::make('is_active')->boolean(),
            Tables\Columns\TextColumn::make('sort_order')->sortable(),
        ])->filters([])->actions([
            Tables\Actions\EditAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
        ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSliders::route('/'),
            'create' => Pages\CreateSlider::route('/create'),
            'edit' => Pages\EditSlider::route('/{record}/edit'),
        ];
    }
}
