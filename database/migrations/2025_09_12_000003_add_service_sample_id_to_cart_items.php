<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_items', 'service_sample_id')) {
                $table->foreignId('service_sample_id')->nullable()->after('service_id')->constrained('service_samples')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'service_sample_id')) {
                $table->dropConstrainedForeignId('service_sample_id');
            }
        });
    }
};


