<?php

namespace App\Filament\Pages\Reports\Concerns;

trait AuthorizesReport
{
    public static function canAccess(): bool
    {
        $user = auth()->user();
        return $user && ($user->hasRole('super_admin') || $user->can('reports.view'));
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
