<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('tracking_number')->nullable()->after('notes');
            $table->string('courier_service', 50)->nullable()->after('tracking_number');
            $table->timestamp('shipped_at')->nullable()->after('courier_service');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            $table->timestamp('cancelled_at')->nullable()->after('delivered_at');
            $table->text('admin_notes')->nullable()->after('cancelled_at');
            $table->decimal('refund_amount', 10, 2)->nullable()->after('admin_notes');
            $table->timestamp('refunded_at')->nullable()->after('refund_amount');
            $table->foreignId('admin_id')->nullable()->after('refunded_at')->constrained('users')->nullOnDelete();
        });

        Schema::create('order_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('event', 50);  // created, status_changed, payment_updated, note_added, shipped, delivered, cancelled, refunded, item_added, item_removed
            $table->string('title');
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->index(['order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_activities');
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('admin_id');
            $table->dropColumn([
                'tracking_number', 'courier_service',
                'shipped_at', 'delivered_at', 'cancelled_at',
                'admin_notes', 'refund_amount', 'refunded_at',
            ]);
        });
    }
};
