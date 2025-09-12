<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->foreignId('design_id')->nullable()->after('service_id')->constrained('designs')->nullOnDelete();
            $table->string('design_preview_path')->nullable()->after('custom_requirements');
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('design_id');
            $table->dropColumn('design_preview_path');
        });
    }
};


