<?php

namespace App\Filament\Forms\Actions;

use App\Models\MediaLibraryItem;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;

/**
 * "Browse Media Library" — opens a WordPress-style grid picker.
 *
 * Drop into any resource form like this:
 *
 *   Forms\Components\Actions::make([
 *       PickFromLibraryAction::make('product_thumbnail'),
 *   ]),
 *   SpatieMediaLibraryFileUpload::make('product_thumbnail')->collection('product_thumbnail'),
 */
class PickFromLibraryAction
{
    public static function make(string $collection, ?string $label = null): Action
    {
        return Action::make('pickFromLibrary_' . $collection)
            ->label($label ?? 'Browse Media Library')
            ->icon('heroicon-o-photo')
            ->color('gray')
            ->modalHeading('Pick from Media Library')
            ->modalWidth('7xl')
            ->modalSubmitActionLabel('Use selected file')
            ->form([
                // Hidden field — populated by the grid view via Alpine + $wire.set
                Forms\Components\Hidden::make('library_item_id')
                    ->required(),
                Forms\Components\View::make('filament.actions.library-picker-grid')
                    ->viewData(['collection' => $collection]),
            ])
            ->action(function (array $data, $record, $livewire) use ($collection) {
                if (!$record) {
                    Notification::make()
                        ->title('Save the record first')
                        ->body('Create and save this record before attaching media from the library.')
                        ->warning()->send();
                    return;
                }

                $item = MediaLibraryItem::find($data['library_item_id'] ?? null);
                $libraryMedia = $item?->getFirstMedia('library');
                if (!$libraryMedia) {
                    Notification::make()->title('Please select a file first.')->danger()->send();
                    return;
                }

                // For singleFile collections, replace any existing
                $config = $record->getMediaCollection($collection);
                if ($config && $config->singleFile) {
                    foreach ($record->getMedia($collection) as $existing) {
                        $existing->delete();
                    }
                }

                $record->addMediaFromDisk($libraryMedia->getPathRelativeToRoot(), $libraryMedia->disk)
                    ->preservingOriginal()
                    ->usingName($item->title ?: $libraryMedia->name)
                    ->usingFileName(basename($libraryMedia->file_name))
                    ->toMediaCollection($collection, 'public');

                Notification::make()
                    ->title('Attached from library')
                    ->body($item->title)
                    ->success()->send();

                if (method_exists($livewire, 'fillForm')) {
                    $livewire->fillForm($record->fresh()->toArray());
                }
            });
    }
}
