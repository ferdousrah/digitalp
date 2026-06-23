<?php

namespace App\Filament\Resources\ProductQuestionResource\Pages;

use App\Filament\Resources\ProductQuestionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProductQuestion extends EditRecord
{
    protected static string $resource = ProductQuestionResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\DeleteAction::make()];
    }

    /** Stamp the answer time the first time an answer is written. */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (! empty($data['answer']) && empty($this->record->answered_at)) {
            $data['answered_at'] = now();
        }
        if (empty($data['answer'])) {
            $data['answered_at'] = null;
        }

        return $data;
    }
}
