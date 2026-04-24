<?php

namespace App\Filament\Resources\CompanyTimelineResource\Pages;

use App\Filament\Resources\CompanyTimelineResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCompanyTimeline extends EditRecord
{
    protected static string $resource = CompanyTimelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
