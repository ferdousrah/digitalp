<?php

namespace App\Filament\Resources\MediaLibraryItemResource\Pages;

use App\Filament\Resources\MediaLibraryItemResource;
use App\Models\MediaLibraryItem;
use Filament\Actions;
use Filament\Forms;
use Filament\Resources\Pages\ListRecords;

class ListMediaLibraryItems extends ListRecords
{
    protected static string $resource = MediaLibraryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Bulk-upload action — uploads N files at once, creates one record per file
            Actions\Action::make('bulkUpload')
                ->label('Upload files')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->modalHeading('Upload files to library')
                ->modalSubmitActionLabel('Upload')
                ->form([
                    Forms\Components\FileUpload::make('files')
                        ->label('Drop files here or click to choose')
                        ->multiple()
                        ->disk('public')
                        ->directory('media-library/staging')
                        ->preserveFilenames()
                        ->maxSize(10240)
                        ->reorderable()
                        ->panelLayout('grid')
                        ->required(),
                    Forms\Components\TagsInput::make('tags')
                        ->placeholder('Optional tags applied to every file (e.g. banner, hero)'),
                ])
                ->action(function (array $data) {
                    $tags = $data['tags'] ?? [];

                    foreach ((array) ($data['files'] ?? []) as $path) {
                        $absolute = storage_path('app/public/' . $path);
                        if (!is_file($absolute)) continue;

                        $item = MediaLibraryItem::create([
                            'title'       => pathinfo($path, PATHINFO_FILENAME),
                            'alt_text'    => null,
                            'tags'        => $tags ?: null,
                            'uploaded_by' => auth()->id(),
                        ]);

                        $item
                            ->addMedia($absolute)
                            ->preservingOriginal(false)  // moves into final spot, deletes the staging copy
                            ->toMediaCollection('library', 'public');
                    }

                    \Filament\Notifications\Notification::make()
                        ->title('Files uploaded')
                        ->success()
                        ->send();
                }),

            Actions\CreateAction::make()
                ->label('New (single)')
                ->icon('heroicon-o-plus')
                ->color('gray'),
        ];
    }
}
