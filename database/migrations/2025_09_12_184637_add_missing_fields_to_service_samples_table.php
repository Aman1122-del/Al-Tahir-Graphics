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
        Schema::table('service_samples', function (Blueprint $table) {
            // Add sample type field
            $table->string('sample_type')->default('standard')->after('sub_category');
            
            // Add price display field for custom pricing text
            $table->string('price_display')->nullable()->after('price');
            
            // Rename price to unit_price to be more explicit
            $table->renameColumn('price', 'unit_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_samples', function (Blueprint $table) {
            $table->dropColumn(['sample_type', 'price_display']);
            $table->renameColumn('unit_price', 'price');
        });
    }
};