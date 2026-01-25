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
        Schema::table('cart_items', function (Blueprint $table) {
            if (!Schema::hasColumn('cart_items', 'wedding_details')) {
                $table->json('wedding_details')->nullable()->after('custom_requirements');
            }
            if (!Schema::hasColumn('cart_items', 'visiting_card_details')) {
                $table->json('visiting_card_details')->nullable()->after('wedding_details');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'wedding_details')) {
                $table->json('wedding_details')->nullable()->after('custom_requirements');
            }
            if (!Schema::hasColumn('order_items', 'visiting_card_details')) {
                $table->json('visiting_card_details')->nullable()->after('wedding_details');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'wedding_details')) {
                $table->dropColumn('wedding_details');
            }
            if (Schema::hasColumn('cart_items', 'visiting_card_details')) {
                $table->dropColumn('visiting_card_details');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'wedding_details')) {
                $table->dropColumn('wedding_details');
            }
            if (Schema::hasColumn('order_items', 'visiting_card_details')) {
                $table->dropColumn('visiting_card_details');
            }
        });
    }
};
