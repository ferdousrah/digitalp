<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view_products', 'create_products', 'edit_products', 'delete_products',
            'view_categories', 'create_categories', 'edit_categories', 'delete_categories',
            'view_pages', 'create_pages', 'edit_pages', 'delete_pages',
            'view_blog', 'create_blog', 'edit_blog', 'delete_blog',
            'view_contacts', 'manage_contacts',
            'view_settings', 'manage_settings',
            'view_users', 'create_users', 'edit_users', 'delete_users',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        Role::create(['name' => 'super-admin'])->givePermissionTo(Permission::all());
        Role::create(['name' => 'editor'])->givePermissionTo([
            'view_products', 'create_products', 'edit_products',
            'view_categories', 'view_pages', 'create_pages', 'edit_pages',
            'view_blog', 'create_blog', 'edit_blog',
            'view_contacts',
        ]);
        Role::create(['name' => 'moderator'])->givePermissionTo([
            'view_products', 'view_categories', 'view_pages',
            'view_blog', 'view_contacts', 'manage_contacts',
        ]);
    }
}
