<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSetting extends CreateRecord
{
    protected static string $resource = SettingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // When type is image, copy the uploaded file path to value
        if (($data['type'] ?? '') === 'image' && isset($data['image_upload'])) {
            $upload = $data['image_upload'];
            $data['value'] = is_array($upload) ? (array_values($upload)[0] ?? null) : $upload;
        }
        unset($data['image_upload']);

        return $data;
    }
}
