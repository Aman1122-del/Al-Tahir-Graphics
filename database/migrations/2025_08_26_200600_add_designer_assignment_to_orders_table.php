<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_designer_id')->nullable()->after('user_id');
            $table->enum('design_status', ['pending', 'in_progress', 'review', 'approved', 'completed'])->default('pending')->after('order_status');
            $table->date('design_due_date')->nullable()->after('design_status');
            $table->text('design_notes')->nullable()->after('design_due_date');
            
            $table->foreign('assigned_designer_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['assigned_designer_id']);
            $table->dropColumn(['assigned_designer_id', 'design_status', 'design_due_date', 'design_notes']);
        });
    }
};
