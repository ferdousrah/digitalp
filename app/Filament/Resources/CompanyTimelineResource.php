<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CompanyTimelineResource\Pages;
use App\Models\CompanyTimeline;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class CompanyTimelineResource extends Resource
{
    protected static ?string $model = CompanyTimeline::class;
    protected static ?string $navigationIcon = 'heroicon-o-clock';
    protected static ?string $navigationGroup = 'Company';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->required()->maxLength(255),
            Forms\Components\Textarea::make('description')->rows(4),
            Forms\Components\TextInput::make('year')->maxLength(4),
            Forms\Components\TextInput::make('icon')->maxLength(255),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
            SpatieMediaLibraryFileUpload::make('timeline_image')->collection('timeline_image')->image(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('year')->sortable(),
            Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
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
            'index' => Pages\ListCompanyTimelines::route('/'),
            'create' => Pages\CreateCompanyTimeline::route('/create'),
            'edit' => Pages\EditCompanyTimeline::route('/{record}/edit'),
        ];
    }
}
