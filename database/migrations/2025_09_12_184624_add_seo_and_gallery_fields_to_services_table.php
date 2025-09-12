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
            // Gallery support (JSON field for multiple images)
            $table->json('gallery_images')->nullable()->after('image_path');
            
            // SEO fields
            $table->string('meta_title')->nullable()->after('gallery_images');
            $table->text('meta_description')->nullable()->after('meta_title');
            $table->text('meta_keywords')->nullable()->after('meta_description');
            
            // Make price field decimal instead of string
            $table->decimal('price', 10, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'gallery_images',
                'meta_title', 
                'meta_description',
                'meta_keywords'
            ]);
            $table->string('price')->change();
        });
    }
};