<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use Illuminate\Support\Str;

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
                'title' => 'Mug Printing',
                'description' => 'Customized mug printing for gifts, corporate branding, and promotional items.',
                'price' => 2499.00,
                'price_display' => 'From PKR 2,499',
                'image_url' => 'random2.png',
                'category' => 'marketing',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 5,
            ],
            [
                'title' => 'Bill book printing',
                'description' => 'Professional bill books, invoices, and receipt books with custom branding and carbonless options.',
                'price' => 1999.00,
                'price_display' => 'From PKR 1,999',
                'image_url' => 'Posters.jpg',
                'category' => 'marketing',
                'is_featured' => false,
                'is_active' => true,
                'sort_order' => 6,
            ],
        ];

        $usedSlugs = [];
        foreach ($services as $service) {
            $base = Str::slug($service['title']);
            $slug = $base;
            $suffix = 2;
            while (in_array($slug, $usedSlugs, true) || Service::where('slug', $slug)->exists()) {
                $slug = $base . '-' . $suffix;
                $suffix++;
            }
            $usedSlugs[] = $slug;
            $service['slug'] = $slug;

            Service::updateOrCreate(
                ['title' => $service['title']],
                $service
            );
        }
    }
}
