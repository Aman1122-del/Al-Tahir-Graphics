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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // Unique order identifier
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null'); // For guest orders
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('customer_phone');
            $table->text('shipping_address');
            $table->string('shipping_method')->default('standard'); // standard, express
            $table->decimal('subtotal', 10, 2);
            $table->decimal('shipping_cost', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('payment_method'); // pay_on_delivery, manual_transfer
            $table->string('payment_status')->default('pending'); // pending, pending_verification, paid, failed
            $table->string('order_status')->default('pending'); // pending, processing, completed, cancelled
            $table->string('payment_screenshot_path')->nullable(); // Path to uploaded payment screenshot
            $table->boolean('account_details_sent')->default(false); // Admin flag for payment details
            $table->text('admin_notes')->nullable(); // Admin notes about the order
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['order_number']);
            $table->index(['user_id']);
            $table->index(['customer_email']);
            $table->index(['payment_status']);
            $table->index(['order_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
