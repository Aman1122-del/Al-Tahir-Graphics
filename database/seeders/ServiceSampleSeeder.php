<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceSample;
use Illuminate\Support\Str;

class ServiceSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Wedding Cards - 10 samples with sub-categories
        $weddingCardService = Service::where('title', 'Wedding Cards')->first();
        if ($weddingCardService) {
            $weddingCardSamples = [
                // Traditional Wedding Cards
                [
                    'title' => 'Royal Gold Foil Wedding Card',
                    'slug' => 'royal-gold-foil-wedding-card',
                    'description' => 'Luxurious wedding invitation with gold foil stamping, embossed borders, and premium cardstock. Perfect for elegant traditional ceremonies.',
                    'price' => 4999.00,
                    'sub_category' => 'Traditional',
                    'image_path' => 'Wedding Card sample 01.jpg',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'title' => 'Classic Ivory Embossed Card',
                    'slug' => 'classic-ivory-embossed-card',
                    'description' => 'Elegant ivory wedding card with embossed floral patterns and traditional gold text printing.',
                    'price' => 3999.00,
                    'sub_category' => 'Traditional',
                    'image_path' => 'Wedding Card sample 02.jpg',
                    'is_active' => true,
                    'sort_order' => 2,
                ],
                [
                    'title' => 'Heritage Red Silk Wedding Card',
                    'slug' => 'heritage-red-silk-wedding-card',
                    'description' => 'Traditional red silk wedding invitation with golden threads and cultural motifs.',
                    'price' => 5499.00,
                    'sub_category' => 'Traditional',
                    'image_path' => 'Wedding Card sample 03.jpg',
                    'is_active' => true,
                    'sort_order' => 3,
                ],
                
                // Modern Wedding Cards
                [
                    'title' => 'Minimalist White Wedding Card',
                    'slug' => 'minimalist-white-wedding-card',
                    'description' => 'Clean, modern design with minimalist typography and subtle geometric patterns.',
                    'price' => 3499.00,
                    'sub_category' => 'Modern',
                    'image_path' => 'Wedding Card sample 04.jpg',
                    'is_active' => true,
                    'sort_order' => 4,
                ],
                [
                    'title' => 'Blush Pink Watercolor Card',
                    'slug' => 'blush-pink-watercolor-card',
                    'description' => 'Contemporary watercolor design with soft blush tones and modern calligraphy.',
                    'price' => 4199.00,
                    'sub_category' => 'Modern',
                    'image_path' => 'Invitation card (wedding).jpg',
                    'is_active' => true,
                    'sort_order' => 5,
                ],
                [
                    'title' => 'Botanical Green Wedding Invite',
                    'slug' => 'botanical-green-wedding-invite',
                    'description' => 'Nature-inspired design with botanical illustrations and eco-friendly paper.',
                    'price' => 3799.00,
                    'sub_category' => 'Modern',
                    'image_path' => 'Invitation card (wedding).jpg',
                    'is_active' => true,
                    'sort_order' => 6,
                ],
                
                // Luxury Wedding Cards
                [
                    'title' => 'Platinum Crystal Wedding Card',
                    'slug' => 'platinum-crystal-wedding-card',
                    'description' => 'Ultra-luxury invitation with crystal embellishments, platinum foil, and velvet box packaging.',
                    'price' => 8999.00,
                    'sub_category' => 'Luxury',
                    'image_path' => 'Wedding Card sample 01.jpg',
                    'is_active' => true,
                    'sort_order' => 7,
                ],
                [
                    'title' => 'Rose Gold Laser Cut Card',
                    'slug' => 'rose-gold-laser-cut-card',
                    'description' => 'Intricate laser-cut design with rose gold accents and premium silk lining.',
                    'price' => 6999.00,
                    'sub_category' => 'Luxury',
                    'image_path' => 'Wedding Card sample 02.jpg',
                    'is_active' => true,
                    'sort_order' => 8,
                ],
                
                // Digital/Animated Cards
                [
                    'title' => 'Digital Video Wedding Invitation',
                    'slug' => 'digital-video-wedding-invitation',
                    'description' => 'Animated digital invitation with personalized video message and interactive RSVP.',
                    'price' => 2999.00,
                    'sub_category' => 'Digital',
                    'image_path' => 'Wedding Card sample 03.jpg',
                    'is_active' => true,
                    'sort_order' => 9,
                ],
                [
                    'title' => 'Interactive Wedding E-Card',
                    'slug' => 'interactive-wedding-e-card',
                    'description' => 'Modern digital wedding card with interactive elements, music, and online RSVP system.',
                    'price' => 1999.00,
                    'sub_category' => 'Digital',
                    'image_path' => 'Wedding Card sample 04.jpg',
                    'is_active' => true,
                    'sort_order' => 10,
                ],
            ];

            foreach ($weddingCardSamples as $sample) {
                $sample['service_id'] = $weddingCardService->id;
                ServiceSample::updateOrCreate(
                    ['slug' => $sample['slug']],
                    $sample
                );
            }
        }

        // Visiting Cards - 2 samples with sub-categories
        $visitingCardService = Service::where('title', 'Visiting Cards')->first();
        if ($visitingCardService) {
            $visitingCardSamples = [
                [
                    'title' => 'Executive Matte Finish Card',
                    'slug' => 'executive-matte-finish-card',
                    'description' => 'Professional business card with matte finish, embossed logo, and premium cardstock for corporate executives.',
                    'price' => 1299.00,
                    'sub_category' => 'Premium',
                    'image_path' => 'Visiting Card sample 01.jpg',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'title' => 'Standard Glossy Business Card',
                    'slug' => 'standard-glossy-business-card',
                    'description' => 'High-quality business card with glossy finish and vibrant color printing, perfect for networking.',
                    'price' => 999.00,
                    'sub_category' => 'Standard',
                    'image_path' => 'Visiting Card sample 02.jpg',
                    'is_active' => true,
                    'sort_order' => 2,
                ],
            ];

            foreach ($visitingCardSamples as $sample) {
                $sample['service_id'] = $visitingCardService->id;
                ServiceSample::updateOrCreate(
                    ['slug' => $sample['slug']],
                    $sample
                );
            }
        }

        // Placeholder samples for other services
        $otherServices = [
            'Flyers' => [
                'sub_categories' => ['Standard', 'Premium'],
                'base_price' => 1499.00,
                'image' => 'Folded brochure.jpg'
            ],
            'Banners' => [
                'sub_categories' => ['Vinyl', 'Fabric', 'Mesh'],
                'base_price' => 2999.00,
                'image' => 'Banners.jpg'
            ],
            'Brochures' => [
                'sub_categories' => ['Bi-fold', 'Tri-fold', 'Z-fold'],
                'base_price' => 2499.00,
                'image' => 'random2.png'
            ],
            'Posters' => [
                'sub_categories' => ['A3', 'A2', 'A1'],
                'base_price' => 1999.00,
                'image' => 'Posters.jpg'
            ],
        ];

        foreach ($otherServices as $serviceTitle => $config) {
            $service = Service::where('title', $serviceTitle)->first();
            if ($service) {
                $counter = 1;
                foreach ($config['sub_categories'] as $subCategory) {
                    $sample = [
                        'service_id' => $service->id,
                        'title' => $subCategory . ' ' . $serviceTitle,
                        'slug' => Str::slug($subCategory . ' ' . $serviceTitle),
                        'description' => 'Professional ' . strtolower($serviceTitle) . ' in ' . $subCategory . ' format with high-quality printing and design.',
                        'price' => $config['base_price'] + ($counter * 200),
                        'sub_category' => $subCategory,
                        'image_path' => $config['image'],
                        'is_active' => true,
                        'sort_order' => $counter,
                    ];

                    ServiceSample::updateOrCreate(
                        ['slug' => $sample['slug']],
                        $sample
                    );

                    $counter++;
                }
            }
        }
    }
}