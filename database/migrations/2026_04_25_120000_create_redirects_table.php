<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('redirects', function (Blueprint $table) {
            $table->id();
            $table->string('source_path', 500);                 // e.g. /old-product
            $table->string('target_path', 500);                 // e.g. /products/new-slug
            $table->unsignedSmallInteger('status_code')->default(301); // 301 / 302 / 307 / 308
            $table->unsignedInteger('hits')->default(0);
            $table->timestamp('last_hit_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->boolean('is_auto')->default(false);         // true if auto-created by slug-change observer
            $table->timestamps();

            // Source path must be unique among active redirects — but we leave deleted ones alone
            $table->unique(['source_path']);
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('redirects');
    }
};
