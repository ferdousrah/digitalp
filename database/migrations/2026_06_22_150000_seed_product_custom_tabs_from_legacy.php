<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;

/**
 * The product detail page now shows only admin-defined Custom Tabs (plus the fixed Q&A +
 * Reviews). This one-time migration converts each product's existing structured attributes +
 * free-form specifications into a "Specifications" custom tab, and its description into a
 * "Details" custom tab — so live products don't lose their content on deploy. Products that
 * already have custom tabs are left untouched.
 */
return new class extends Migration
{
    public function up(): void
    {
        Product::withTrashed()
            ->with(['attributeValues.attribute'])
            ->chunkById(100, function ($products) {
                foreach ($products as $p) {
                    if (! empty($p->custom_tabs)) {
                        continue;
                    }

                    $tabs = [];

                    // Specifications: structured attribute values + free-form specs → an HTML table.
                    $rows = '';
                    foreach ($p->attributeValues as $av) {
                        if ($av->attribute && filled($av->value)) {
                            $rows .= '<tr><th style="text-align:left;padding:10px 16px;width:35%;font-weight:600;">'
                                . e($av->attribute->name) . '</th><td style="padding:10px 16px;">' . e($av->value) . '</td></tr>';
                        }
                    }
                    foreach ((array) $p->specifications as $k => $v) {
                        if (filled($v)) {
                            $rows .= '<tr><th style="text-align:left;padding:10px 16px;width:35%;font-weight:600;">'
                                . e($k) . '</th><td style="padding:10px 16px;">' . e($v) . '</td></tr>';
                        }
                    }
                    if ($rows !== '') {
                        $tabs[] = ['title' => 'Specifications', 'content' => '<table style="width:100%;border-collapse:collapse;">' . $rows . '</table>'];
                    }

                    // Details: the rich description.
                    if (filled($p->description)) {
                        $tabs[] = ['title' => 'Details', 'content' => $p->description];
                    }

                    if ($tabs) {
                        $p->custom_tabs = $tabs;
                        $p->saveQuietly(); // no slug regen / no Scout reindex
                    }
                }
            });
    }

    public function down(): void
    {
        // Irreversible content migration.
    }
};
