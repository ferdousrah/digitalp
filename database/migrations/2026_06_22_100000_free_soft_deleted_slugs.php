<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * One-time cleanup for the slug-reuse fix (see App\Models\Concerns\ReusableSlug):
 * rename already soft-deleted rows' slugs to "{slug}__deleted__{id}" so the original
 * slug is freed for re-use. New deletes are handled automatically by the trait.
 */
return new class extends Migration
{
    private array $tables = ['categories', 'brands', 'blog_posts', 'services', 'pages'];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table)
                || ! Schema::hasColumn($table, 'deleted_at')
                || ! Schema::hasColumn($table, 'slug')) {
                continue;
            }

            DB::table($table)
                ->whereNotNull('deleted_at')
                ->where('slug', 'not like', '%\_\_deleted\_\_%') // skip already-freed slugs
                ->update(['slug' => DB::raw("CONCAT(slug, '__deleted__', id)")]);
        }
    }

    public function down(): void
    {
        // Best-effort reverse: strip the "__deleted__{id}" marker from trashed rows.
        foreach ($this->tables as $table) {
            if (! Schema::hasTable($table)
                || ! Schema::hasColumn($table, 'deleted_at')
                || ! Schema::hasColumn($table, 'slug')) {
                continue;
            }

            DB::table($table)
                ->whereNotNull('deleted_at')
                ->where('slug', 'like', '%\_\_deleted\_\_%')
                ->update(['slug' => DB::raw("SUBSTRING_INDEX(slug, '__deleted__', 1)")]);
        }
    }
};
