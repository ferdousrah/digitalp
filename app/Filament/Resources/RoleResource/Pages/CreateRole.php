<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRole extends CreateRecord
{
    protected static string $resource = RoleResource::class;

    protected function afterCreate(): void
    {
        $ids = $this->collectPermissionIds();
        if (empty($ids)) return;

        $permissions = \Spatie\Permission\Models\Permission::whereIn('id', $ids)->get();
        $this->record->syncPermissions($permissions);
    }

    /**
     * Merge every "permission_group_*" checkbox-list back into a single flat
     * array of permission IDs to sync onto the role.
     */
    protected function collectPermissionIds(): array
    {
        $state = $this->form->getRawState();
        $ids = [];
        foreach ($state as $key => $value) {
            if (str_starts_with($key, 'permission_group_') && is_array($value)) {
                $ids = array_merge($ids, $value);
            }
        }
        return array_values(array_unique(array_map('intval', array_filter($ids))));
    }
}
