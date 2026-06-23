<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->text('question');
            $table->text('answer')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->boolean('is_published')->default(false);
            $table->timestamps();

            $table->index(['product_id', 'is_published']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_questions');
    }
};
