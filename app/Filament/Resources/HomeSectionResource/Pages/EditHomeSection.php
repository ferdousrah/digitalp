<?php

namespace App\Filament\Resources\HomeSectionResource\Pages;

use App\Filament\Resources\HomeSectionResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditHomeSection extends EditRecord
{
    protected static string $resource = HomeSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('back')
                ->label('Back to list')
                ->url(static::getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}
