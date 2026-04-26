<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesWithPermission;
use App\Filament\Resources\MediaLibraryItemResource\Pages;
use App\Models\MediaLibraryItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class MediaLibraryItemResource extends Resource
{
    use AuthorizesWithPermission;
    protected static ?string $permissionKey = 'media_library';

    protected static ?string $model = MediaLibraryItem::class;
    protected static ?string $recordTitleAttribute = 'title';
    protected static ?string $navigationIcon = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Content';
    protected static ?string $navigationLabel = 'Media Library';
    protected static ?int $navigationSort = 99;
    protected static ?string $modelLabel = 'media item';
    protected static ?string $pluralModelLabel = 'media library';

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'alt_text', 'description'];
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('File')
                ->schema([
                    SpatieMediaLibraryFileUpload::make('library')
                        ->label('File')
                        ->collection('library')
                        ->disk('public')
                        ->required()
                        ->preserveFilenames()
                        ->downloadable()
                        ->openable()
                        ->maxSize(10240) // 10 MB
                        ->columnSpanFull(),
                ]),

            Forms\Components\Section::make('Details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('alt_text')
                        ->label('Alt text (for accessibility + SEO)')
                        ->helperText('What the image shows. Important for screen readers and image search.')
                        ->maxLength(500)
                        ->columnSpanFull(),
                    Forms\Components\TagsInput::make('tags')
                        ->placeholder('e.g. banner, hero, product')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('description')
                        ->rows(2)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(fn ($record) => Pages\EditMediaLibraryItem::getUrl(['record' => $record]))
            ->defaultSort('created_at', 'desc')

            // Grid layout — visual thumbnails like a real media library
            ->contentGrid([
                'sm' => 2,
                'md' => 3,
                'lg' => 4,
                'xl' => 6,
            ])

            ->columns([
                Tables\Columns\Layout\Stack::make([
                    SpatieMediaLibraryImageColumn::make('library')
                        ->collection('library')
                        ->conversion('thumb')
                        ->label('')
                        ->extraAttributes([
                            'style' => 'width:100%; aspect-ratio:1/1; object-fit:cover; border-radius:8px 8px 0 0;',
                        ]),
                    Tables\Columns\Layout\Stack::make([
                        Tables\Columns\TextColumn::make('title')
                            ->searchable()
                            ->weight('semibold')
                            ->limit(28)
                            ->extraAttributes(['style' => 'padding:8px 10px 0;']),
                        Tables\Columns\TextColumn::make('library_size')
                            ->label('')
                            ->getStateUsing(function (MediaLibraryItem $record) {
                                $bytes = $record->fileSize();
                                if ($bytes === 0) return '—';
                                if ($bytes < 1024) return $bytes . ' B';
                                if ($bytes < 1024 * 1024) return round($bytes / 1024, 1) . ' KB';
                                return round($bytes / 1024 / 1024, 2) . ' MB';
                            })
                            ->size('xs')
                            ->color('gray')
                            ->extraAttributes(['style' => 'padding:0 10px 8px;']),
                    ])->space(0),
                ])->space(0),
            ])

            ->filters([
                Tables\Filters\Filter::make('images_only')
                    ->label('Images only')
                    ->query(fn ($q) => $q->whereHas('media', fn ($m) => $m->where('mime_type', 'like', 'image/%')))
                    ->toggle(),
                Tables\Filters\Filter::make('large')
                    ->label('Large files (> 1 MB)')
                    ->query(fn ($q) => $q->whereHas('media', fn ($m) => $m->where('size', '>', 1024 * 1024)))
                    ->toggle(),
            ])

            ->actions([
                Tables\Actions\Action::make('copyUrl')
                    ->label('Copy URL')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray')
                    ->action(function (MediaLibraryItem $record, $livewire) {
                        $url = $record->url();
                        $livewire->js('navigator.clipboard.writeText(' . json_encode($url) . ');');
                        \Filament\Notifications\Notification::make()
                            ->title('URL copied')
                            ->body($url)
                            ->success()
                            ->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Delete this file?')
                    ->modalDescription('This will permanently delete the file. Make sure no live page is referencing its URL.'),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListMediaLibraryItems::route('/'),
            'create' => Pages\CreateMediaLibraryItem::route('/create'),
            'edit'   => Pages\EditMediaLibraryItem::route('/{record}/edit'),
        ];
    }
}
