<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesWithPermission;
use App\Filament\Resources\ProductQuestionResource\Pages;
use App\Models\ProductQuestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductQuestionResource extends Resource
{
    use AuthorizesWithPermission;
    protected static ?string $permissionKey = 'product_questions';

    protected static ?string $model = ProductQuestion::class;
    protected static ?string $navigationIcon  = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Product Q&A';
    protected static ?int    $navigationSort  = 5;

    /** Badge showing how many questions are still awaiting an answer. */
    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::whereNull('answer')->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Question')->schema([
                Forms\Components\TextInput::make('product.name')->label('Product')->disabled(),
                Forms\Components\TextInput::make('name')->label('Asked by')->disabled(),
                Forms\Components\Textarea::make('question')->disabled()->rows(3)->columnSpanFull(),
            ])->columns(2),

            Forms\Components\Section::make('Your answer')->schema([
                Forms\Components\Textarea::make('answer')
                    ->rows(4)->columnSpanFull()
                    ->helperText('Write a public answer. Saving with an answer + "Published" shows it on the product page.'),
                Forms\Components\Toggle::make('is_published')
                    ->label('Published (visible on the product page)')->default(true),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->defaultSort('created_at', 'desc')->columns([
            Tables\Columns\TextColumn::make('product.name')->label('Product')->limit(40)->searchable()->sortable(),
            Tables\Columns\TextColumn::make('name')->label('By')->searchable(),
            Tables\Columns\TextColumn::make('question')->limit(60)->wrap(),
            Tables\Columns\IconColumn::make('answer')->label('Answered')
                ->state(fn ($record) => filled($record->answer))->boolean(),
            Tables\Columns\IconColumn::make('is_published')->label('Published')->boolean(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(),
        ])->filters([
            Tables\Filters\TernaryFilter::make('answered')
                ->label('Answered')
                ->queries(
                    true: fn ($q) => $q->whereNotNull('answer'),
                    false: fn ($q) => $q->whereNull('answer'),
                ),
            Tables\Filters\TernaryFilter::make('is_published')->label('Published'),
        ])->actions([
            Tables\Actions\EditAction::make()->label('Answer'),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
        ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductQuestions::route('/'),
            'edit'  => Pages\EditProductQuestion::route('/{record}/edit'),
        ];
    }
}
