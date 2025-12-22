<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_item_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['return', 'cancellation'])->default('return');
            $table->enum('reason', [
                'defective_product',
                'wrong_item',
                'not_as_described',
                'changed_mind',
                'late_delivery',
                'other'
            ]);
            $table->text('description');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed', 'refunded'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->string('refund_method')->nullable();
            $table->string('tracking_number')->nullable();
            $table->json('images')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_requests');
    }
};
