<?php

namespace App\Filament\Resources\RoleResource\Pages;

use App\Filament\Resources\RoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditRole extends EditRecord
{
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->hidden(fn () => in_array($this->record->name, ['super_admin', 'admin'])),
        ];
    }

    /**
     * When loading the form, pre-fill the grouped checkbox-lists with the
     * IDs of permissions this role already has, bucketed by resource label.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        $groups = RoleResource::groupedPermissions();
        $assigned = $this->record->permissions()->pluck('id')->all();

        foreach ($groups as $label => $options) {
            $data["permission_group_{$label}"] = array_values(
                array_intersect(array_keys($options), $assigned)
            );
        }
        return $data;
    }

    protected function afterSave(): void
    {
        $state = $this->form->getRawState();
        $ids = [];
        foreach ($state as $key => $value) {
            if (str_starts_with($key, 'permission_group_') && is_array($value)) {
                $ids = array_merge($ids, $value);
            }
        }
        $ids = array_values(array_unique(array_map('intval', array_filter($ids))));
        $permissions = \Spatie\Permission\Models\Permission::whereIn('id', $ids)->get();
        $this->record->syncPermissions($permissions);
    }
}
