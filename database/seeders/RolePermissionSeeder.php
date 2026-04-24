<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    /**
     * One permission group per "resource". The key is used to build permission
     * names (`{key}.view`, `{key}.create`, etc.) and the label is displayed in
     * the admin UI (grouped checkbox).
     */
    protected array $resources = [
        // Catalog
        'products'         => 'Products',
        'categories'       => 'Categories',
        'brands'           => 'Brands',
        'attributes'       => 'Attributes',
        // Shop
        'orders'           => 'Orders',
        // Content
        'blog_posts'       => 'Blog Posts',
        'blog_categories'  => 'Blog Categories',
        'pages'            => 'Pages',
        'faqs'             => 'FAQs',
        'faq_categories'   => 'FAQ Categories',
        'services'         => 'Services',
        'gallery_albums'   => 'Gallery Albums',
        'testimonials'     => 'Testimonials',
        'sliders'          => 'Sliders',
        'newsletter'       => 'Newsletter Subscribers',
        'contacts'         => 'Contact Submissions',
        'company_timeline' => 'Company Timeline',
        'menu_items'       => 'Menu Items',
        'home_sections'    => 'Homepage Sections',
        'site_contents'    => 'Site Contents',
        // Settings
        'settings'         => 'General Settings',
        'template'         => 'Template / Colors',
        'product_card'     => 'Product Card Settings',
        'payment'          => 'Payment Settings',
        'font'             => 'Font Settings',
        'hero_layout'      => 'Hero Layout',
        'users'            => 'Users',
        'roles'            => 'Roles & Permissions',
    ];

    protected array $actions = ['view', 'create', 'update', 'delete'];

    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Create one permission per (resource, action) pair — idempotent
        foreach ($this->resources as $key => $label) {
            foreach ($this->actions as $action) {
                Permission::firstOrCreate([
                    'name'       => "{$key}.{$action}",
                    'guard_name' => 'web',
                ]);
            }
        }

        // super_admin: bypasses all checks via Gate::before() — no perms attached
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        // admin: explicitly gets all permissions
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        // editor: content + catalog read/write, no deletes, no settings
        $editor = Role::firstOrCreate(['name' => 'editor', 'guard_name' => 'web']);
        $editorPerms = Permission::query()
            ->where(function ($q) {
                $contentResources = [
                    'products', 'categories', 'brands',
                    'blog_posts', 'blog_categories', 'pages',
                    'faqs', 'faq_categories', 'sliders',
                    'gallery_albums', 'testimonials',
                    'home_sections', 'site_contents',
                ];
                foreach ($contentResources as $r) {
                    $q->orWhere('name', 'like', "{$r}.%");
                }
            })
            ->where('name', 'not like', '%.delete')
            ->get();
        $editor->syncPermissions($editorPerms);

        // Promote the first user to super_admin (usually the installer-created admin)
        $firstUser = DB::table('users')->orderBy('id')->first();
        if ($firstUser) {
            $user = User::find($firstUser->id);
            if ($user && !$user->hasRole('super_admin')) {
                $user->assignRole('super_admin');
            }
        }
    }
}
