<?php

namespace App\Filament\Resources\MediaLibraryItemResource\Pages;

use App\Filament\Resources\MediaLibraryItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMediaLibraryItem extends CreateRecord
{
    protected static string $resource = MediaLibraryItemResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['uploaded_by'] = auth()->id();
        if (empty($data['title'])) {
            $data['title'] = 'Untitled';
        }
        return $data;
    }
}
