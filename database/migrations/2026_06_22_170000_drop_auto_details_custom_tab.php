<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;

/**
 * "Description" is now a fixed tab (driven by the product's description field), so the
 * auto-created "Details" custom tab (a copy of the description, added by the earlier
 * legacy-tabs migration) is a duplicate. Remove only those exact auto-created copies;
 * any admin-authored tab is left alone.
 */
return new class extends Migration
{
    public function up(): void
    {
        Product::withTrashed()->chunkById(100, function ($products) {
            foreach ($products as $p) {
                $tabs = $p->custom_tabs ?: [];
                if (empty($tabs)) {
                    continue;
                }

                $filtered = array_values(array_filter($tabs, fn ($t) =>
                    ! (($t['title'] ?? '') === 'Details' && ($t['content'] ?? '') === $p->description)
                ));

                if (count($filtered) !== count($tabs)) {
                    $p->custom_tabs = $filtered ?: null;
                    $p->saveQuietly();
                }
            }
        });
    }

    public function down(): void
    {
        // Irreversible.
    }
};
