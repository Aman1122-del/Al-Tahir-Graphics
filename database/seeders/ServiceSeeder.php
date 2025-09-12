<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'title' => 'Wedding Cards',
                'description' => 'Elegant and personalized wedding invitations with premium finishes including foil stamping, embossing, and specialty papers.',
                'price' => 3999.00,
                'price_display' => 'From PKR 3,999',
                'image_url' => 'Invitation card (wedding).jpg',
                'category' => 'cards',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title' => 'Visiting Cards',
                'description' => 'Professional business cards with various finishes including matte, glossy, and textured papers.',
                'price' => 999.00,
                'price_display' => 'From PKR 999',
                'image_url' => 'buisness cards.jpg',
                'category' => 'cards',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title' => 'Flyers',
                'description' => 'Eye-catching promotional flyers with vibrant colors and high-quality printing for maximum impact.',
                'price' => 1499.00,
                'price_display' => 'From PKR 1,499',
                'image_url' => 'Folded brochure.jpg',
                'category' => 'marketing',
                'is_featured' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
            [
                'title' => 'Banners',
                'description' => 'Large format banners and signage for events, exhibitions, and outdoor advertising.',
                'price' => 2999.00,
                'price_display' => 'From PKR 2,999',
                'image_url' => 'Banners.jpg',
                'category' => 'large-format',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 4,
            ],
            [
                'title' => 'Brochures',
                'description' => 'Professional brochures and catalogs with multiple folds and premium paper options.',
                'price' => 2499.00,
                'price_display' => 'From PKR 2,499',
                'image_url' => 'random2.png',
                'category' => 'marketing',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'title' => 'Posters',
                'description' => 'High-quality posters in various sizes with vibrant colors and crisp details.',
                'price' => 1999.00,
                'price_display' => 'From PKR 1,999',
                'image_url' => 'Posters.jpg',
                'category' => 'marketing',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['title' => $service['title']],
                $service
            );
        }
    }
}
