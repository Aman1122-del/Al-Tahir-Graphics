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
        Schema::table('services', function (Blueprint $table) {
            // Stock management
            $table->integer('stock_quantity')->default(0)->after('price');
            $table->boolean('track_stock')->default(false)->after('stock_quantity');
            $table->boolean('allow_backorder')->default(false)->after('track_stock');
            $table->integer('low_stock_threshold')->default(5)->after('allow_backorder');
            
            // Variants
            $table->json('variants')->nullable()->after('low_stock_threshold'); // Color, size, etc.
            $table->json('variant_options')->nullable()->after('variants'); // Available options
            
            // Enhanced SEO (meta fields already exist, just change meta_keywords to json)
            $table->json('meta_keywords')->nullable()->change();
            
            // Category relationship
            $table->foreignId('category_id')->nullable()->after('category')->constrained()->onDelete('set null');
            
            // Additional fields
            $table->string('sku')->nullable()->after('slug');
            $table->decimal('cost_price', 10, 2)->nullable()->after('price');
            $table->decimal('sale_price', 10, 2)->nullable()->after('cost_price');
            $table->timestamp('sale_start_date')->nullable()->after('sale_price');
            $table->timestamp('sale_end_date')->nullable()->after('sale_start_date');
            $table->integer('weight')->nullable()->after('sale_end_date'); // in grams
            $table->json('dimensions')->nullable()->after('weight'); // length, width, height
            $table->text('short_description')->nullable()->after('description');
            $table->json('tags')->nullable()->after('meta_keywords');
            
            // Indexes
            $table->index(['is_active', 'is_featured']);
            $table->index(['category_id', 'is_active']);
            $table->index('sku');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'stock_quantity',
                'track_stock',
                'allow_backorder',
                'low_stock_threshold',
                'variants',
                'variant_options',
                'meta_title',
                'meta_description',
                'meta_keywords',
                'category_id',
                'sku',
                'cost_price',
                'sale_price',
                'sale_start_date',
                'sale_end_date',
                'weight',
                'dimensions',
                'short_description',
                'tags',
            ]);
            
            $table->dropIndex(['is_active', 'is_featured']);
            $table->dropIndex(['category_id', 'is_active']);
            $table->dropIndex(['sku']);
        });
    }
};
