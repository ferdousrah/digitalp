<?php

namespace App\Filament\Resources\CompanyTimelineResource\Pages;

use App\Filament\Resources\CompanyTimelineResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCompanyTimelines extends ListRecords
{
    protected static string $resource = CompanyTimelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
