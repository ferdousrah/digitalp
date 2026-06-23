<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;

/**
 * "Specifications" is now an automatic tab rendered from the structured Details fields
 * (specifications key/value, weight, dimensions, warranty + attributes). The earlier
 * legacy migration had baked specs into a "Specifications" custom tab — remove only those
 * exact auto-created copies (identified by the generated table markup); admin-authored
 * tabs named "Specifications" with different content are left alone.
 */
return new class extends Migration
{
    private const AUTO_PREFIX = '<table style="width:100%;border-collapse:collapse;">';

    public function up(): void
    {
        Product::withTrashed()->chunkById(100, function ($products) {
            foreach ($products as $p) {
                $tabs = $p->custom_tabs ?: [];
                if (empty($tabs)) {
                    continue;
                }

                $filtered = array_values(array_filter($tabs, fn ($t) =>
                    ! (($t['title'] ?? '') === 'Specifications'
                        && str_starts_with($t['content'] ?? '', self::AUTO_PREFIX))
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
