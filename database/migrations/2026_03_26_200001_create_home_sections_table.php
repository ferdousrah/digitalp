<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('home_sections', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('type');
            $table->string('title')->nullable();
            $table->string('subtitle', 500)->nullable();
            $table->boolean('is_enabled')->default(true);
            $table->integer('sort_order')->default(0);
            $table->string('bg_color')->default('#ffffff');
            $table->integer('padding_y')->default(48);
            $table->string('heading_color')->default('#111827');
            $table->string('heading_size_desktop')->default('28px');
            $table->string('heading_size_mobile')->default('22px');
            $table->string('heading_weight')->default('700');
            $table->string('subheading_color')->default('#6b7280');
            $table->string('subheading_size_desktop')->default('16px');
            $table->string('subheading_size_mobile')->default('14px');
            $table->string('subheading_weight')->default('400');
            $table->string('text_align')->default('center');
            $table->string('display_type')->default('grid');
            $table->integer('desktop_columns')->default(4);
            $table->integer('mobile_columns')->default(2);
            $table->integer('desktop_visible')->default(4);
            $table->integer('mobile_visible')->default(2);
            $table->integer('items_count')->default(8);
            $table->integer('rows')->default(2);
            $table->json('extra')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_sections');
    }
};
