<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Drop this into any Filament Resource to enforce spatie/laravel-permission
 * checks on the 5 standard CRUD actions + sidebar visibility. The resource
 * must also define a static $permissionKey (e.g. 'products'), which is used
 * to build permission names like `products.view`, `products.create`, etc.
 *
 * Users with the `super_admin` role bypass everything via Gate::before() in
 * AppServiceProvider, so nothing to special-case here.
 */
trait AuthorizesWithPermission
{
    protected static function permKey(): ?string
    {
        return property_exists(static::class, 'permissionKey') ? static::$permissionKey : null;
    }

    protected static function userCan(string $action): bool
    {
        $key = static::permKey();
        if (!$key) return true; // resource didn't opt in → always allowed

        $user = auth()->user();
        if (!$user) return false;

        return $user->can("{$key}.{$action}");
    }

    public static function canViewAny(): bool      { return static::userCan('view'); }
    public static function canView(Model $record): bool { return static::userCan('view'); }
    public static function canCreate(): bool       { return static::userCan('create'); }
    public static function canEdit(Model $record): bool { return static::userCan('update'); }
    public static function canDelete(Model $record): bool { return static::userCan('delete'); }
    public static function canDeleteAny(): bool    { return static::userCan('delete'); }
    public static function canForceDelete(Model $record): bool { return static::userCan('delete'); }
    public static function canForceDeleteAny(): bool { return static::userCan('delete'); }
    public static function canRestore(Model $record): bool { return static::userCan('update'); }
    public static function canRestoreAny(): bool { return static::userCan('update'); }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canViewAny();
    }
}
