<?php

namespace App\Filament\Resources\MediaLibraryItemResource\Pages;

use App\Filament\Resources\MediaLibraryItemResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMediaLibraryItem extends EditRecord
{
    protected static string $resource = MediaLibraryItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('copyUrl')
                ->label('Copy URL')
                ->icon('heroicon-o-clipboard-document')
                ->color('gray')
                ->action(function ($livewire) {
                    $url = $this->record->url();
                    $livewire->js('navigator.clipboard.writeText(' . json_encode($url) . ');');
                    \Filament\Notifications\Notification::make()
                        ->title('URL copied')
                        ->body($url)
                        ->success()
                        ->send();
                }),
            Actions\Action::make('view')
                ->label('Open file')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(fn () => $this->record->url())
                ->openUrlInNewTab(),
            Actions\DeleteAction::make(),
        ];
    }
}
