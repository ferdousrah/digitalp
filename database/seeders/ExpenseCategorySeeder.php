<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Salaries',        'color' => '#3b82f6', 'icon' => 'heroicon-o-user-group',        'sort_order' => 1],
            ['name' => 'Rent',            'color' => '#8b5cf6', 'icon' => 'heroicon-o-home',              'sort_order' => 2],
            ['name' => 'Utilities',       'color' => '#f59e0b', 'icon' => 'heroicon-o-bolt',              'sort_order' => 3],
            ['name' => 'Inventory',       'color' => '#10b981', 'icon' => 'heroicon-o-cube',              'sort_order' => 4],
            ['name' => 'Marketing',       'color' => '#ec4899', 'icon' => 'heroicon-o-megaphone',         'sort_order' => 5],
            ['name' => 'Transport',       'color' => '#06b6d4', 'icon' => 'heroicon-o-truck',             'sort_order' => 6],
            ['name' => 'Office Supplies', 'color' => '#64748b', 'icon' => 'heroicon-o-paper-clip',        'sort_order' => 7],
            ['name' => 'Software / SaaS', 'color' => '#6366f1', 'icon' => 'heroicon-o-command-line',      'sort_order' => 8],
            ['name' => 'Fees & Taxes',    'color' => '#dc2626', 'icon' => 'heroicon-o-document-text',     'sort_order' => 9],
            ['name' => 'Other',           'color' => '#94a3b8', 'icon' => 'heroicon-o-ellipsis-horizontal','sort_order' => 99],
        ];

        foreach ($categories as $c) {
            ExpenseCategory::firstOrCreate(
                ['name' => $c['name']],
                array_merge($c, ['is_active' => true])
            );
        }
    }
}
