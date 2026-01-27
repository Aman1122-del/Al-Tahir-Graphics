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
            if (!Schema::hasColumn('cart_items', 'panaflex_details')) {
                $table->json('panaflex_details')->nullable()->after('visiting_card_details');
            }
            if (!Schema::hasColumn('cart_items', 'flyer_brochure_details')) {
                $table->json('flyer_brochure_details')->nullable()->after('panaflex_details');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('order_items', 'panaflex_details')) {
                $table->json('panaflex_details')->nullable()->after('visiting_card_details');
            }
            if (!Schema::hasColumn('order_items', 'flyer_brochure_details')) {
                $table->json('flyer_brochure_details')->nullable()->after('panaflex_details');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cart_items', function (Blueprint $table) {
            if (Schema::hasColumn('cart_items', 'flyer_brochure_details')) {
                $table->dropColumn('flyer_brochure_details');
            }
            if (Schema::hasColumn('cart_items', 'panaflex_details')) {
                $table->dropColumn('panaflex_details');
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'flyer_brochure_details')) {
                $table->dropColumn('flyer_brochure_details');
            }
            if (Schema::hasColumn('order_items', 'panaflex_details')) {
                $table->dropColumn('panaflex_details');
            }
        });
    }
};
