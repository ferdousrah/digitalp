<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

use App\Filament\Concerns\AuthorizesWithPermission;
class BlogPostResource extends Resource
{
    use AuthorizesWithPermission;
    protected static ?string $permissionKey = 'blog_posts';

    protected static ?string $model = BlogPost::class;
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    protected static ?string $navigationGroup = 'Blog';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Post Details')->schema([
                Forms\Components\TextInput::make('title')->required()->maxLength(255),
                Forms\Components\TextInput::make('slug')->maxLength(255),
                Forms\Components\Textarea::make('excerpt')->rows(3),
                Forms\Components\RichEditor::make('content')->columnSpanFull(),
                Forms\Components\Select::make('blog_category_id')->relationship('category', 'name')->searchable()->preload()->nullable(),
                Forms\Components\Select::make('author_id')->relationship('author', 'name')->searchable()->preload()->nullable(),
                Forms\Components\Select::make('status')->options([
                    'draft' => 'Draft',
                    'published' => 'Published',
                    'archived' => 'Archived',
                ])->default('draft'),
                Forms\Components\DateTimePicker::make('published_at'),
                SpatieMediaLibraryFileUpload::make('featured_image')->collection('featured_image')->image(),
            ]),
            Forms\Components\Section::make('SEO')->schema([
                Forms\Components\TextInput::make('meta_title')->maxLength(255),
                Forms\Components\Textarea::make('meta_description')->rows(3),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            SpatieMediaLibraryImageColumn::make('featured_image')->collection('featured_image'),
            Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
            Tables\Columns\TextColumn::make('category.name')->sortable(),
            Tables\Columns\TextColumn::make('author.name')->sortable(),
            Tables\Columns\BadgeColumn::make('status')->colors([
                'warning' => 'draft',
                'success' => 'published',
                'danger' => 'archived',
            ]),
            Tables\Columns\TextColumn::make('published_at')->dateTime()->sortable(),
            Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
