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
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('size')->nullable()->after('custom_requirements');
            $table->string('paper_type')->nullable()->after('size');
            $table->string('finish')->nullable()->after('paper_type');
            $table->foreignId('service_sample_id')->nullable()->after('service_id')->constrained()->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['size', 'paper_type', 'finish', 'service_sample_id']);
        });
    }
};
