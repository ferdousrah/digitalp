<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * `weight` was decimal(8,2), so admins couldn't enter a unit ("100gm", "1.63 kg") — it
 * 1366-errored. Weight is display-only (never used in a calculation), so make it a free
 * string like `dimensions`/`warranty_info`.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('weight')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('weight', 8, 2)->nullable()->change();
        });
    }
};
