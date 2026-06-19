<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->json('faqs')->nullable()->after('specifications');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->json('faqs')->nullable()->after('description');
            $table->longText('seo_content')->nullable()->after('description');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('faqs');
        });
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['faqs', 'seo_content']);
        });
    }
};
