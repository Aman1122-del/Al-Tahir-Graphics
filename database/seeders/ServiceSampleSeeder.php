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
                    'unit_price' => 4999.00,
                    'sub_category' => 'Traditional',
                    'image_path' => 'Wedding Card sample 01.jpg',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'title' => 'Classic Ivory Embossed Card',
                    'slug' => 'classic-ivory-embossed-card',
                    'description' => 'Elegant ivory wedding card with embossed floral patterns and traditional gold text printing.',
                    'unit_price' => 3999.00,
                    'sub_category' => 'Traditional',
                    'image_path' => 'images/wedding-cards/classic-lvory-embossed-card.jpg',
                    'is_active' => true,
                    'sort_order' => 2,
                ],
                [
                    'title' => 'Heritage Red Silk Wedding Card',
                    'slug' => 'heritage-red-silk-wedding-card',
                    'description' => 'Traditional red silk wedding invitation with golden threads and cultural motifs.',
                    'unit_price' => 5499.00,
                    'sub_category' => 'Traditional',
                    'image_path' => 'images/wedding-cards/heritage-red-silk-wedding-card.jpg',
                    'is_active' => true,
                    'sort_order' => 3,
                ],

                // Modern Wedding Cards
                [
                    'title' => 'Minimalist White Wedding Card',
                    'slug' => 'minimalist-white-wedding-card',
                    'description' => 'Clean, modern design with minimalist typography and subtle geometric patterns.',
                    'unit_price' => 3499.00,
                    'sub_category' => 'Modern',
                    'image_path' => 'images/wedding-cards/minimalist-white-wedding-card.jpg',
                    'is_active' => true,
                    'sort_order' => 4,
                ],
                [
                    'title' => 'Blush Pink Watercolor Card',
                    'slug' => 'blush-pink-watercolor-card',
                    'description' => 'Contemporary watercolor design with soft blush tones and modern calligraphy.',
                    'unit_price' => 4199.00,
                    'sub_category' => 'Modern',
                    'image_path' => 'images/wedding-cards/blush-pink-watercolor-card.jpg',
                    'is_active' => true,
                    'sort_order' => 5,
                ],
                [
                    'title' => 'Botanical Green Wedding Invite',
                    'slug' => 'botanical-green-wedding-invite',
                    'description' => 'Nature-inspired design with botanical illustrations and eco-friendly paper.',
                    'unit_price' => 3799.00,
                    'sub_category' => 'Modern',
                    'image_path' => 'images/wedding-cards/botanical-green-wedding-invite.jpg',
                    'is_active' => true,
                    'sort_order' => 6,
                ],

                // Luxury Wedding Cards
                [
                    'title' => 'Platinum Crystal Wedding Card',
                    'slug' => 'platinum-crystal-wedding-card',
                    'description' => 'Ultra-luxury invitation with crystal embellishments, platinum foil, and velvet box packaging.',
                    'unit_price' => 8999.00,
                    'sub_category' => 'Luxury',
                    'image_path' => 'images/wedding-cards/platinum-crystal-wedding-card.jpg',
                    'is_active' => true,
                    'sort_order' => 7,
                ],
                [
                    'title' => 'Rose Gold Laser Cut Card',
                    'slug' => 'rose-gold-laser-cut-card',
                    'description' => 'Intricate laser-cut design with rose gold accents and premium silk lining.',
                    'unit_price' => 6999.00,
                    'sub_category' => 'Luxury',
                    'image_path' => 'images/wedding-cards/rose-gold-laser-cut-card.jpg',
                    'is_active' => true,
                    'sort_order' => 8,
                ],

                // Digital/Animated Cards
                [
                    'title' => 'Digital Video Wedding Invitation',
                    'slug' => 'digital-video-wedding-invitation',
                    'description' => 'Animated digital invitation with personalized video message and interactive RSVP.',
                    'unit_price' => 2999.00,
                    'sub_category' => 'Digital',
                    'image_path' => 'images/wedding-cards/digital-video-wedding_invitation.jpg',
                    'is_active' => true,
                    'sort_order' => 9,
                ],
                [
                    'title' => 'Interactive Wedding E-Card',
                    'slug' => 'interactive-wedding-e-card',
                    'description' => 'Modern digital wedding card with interactive elements, music, and online RSVP system.',
                    'unit_price' => 1999.00,
                    'sub_category' => 'Digital',
                    'image_path' => 'images/wedding-cards/interactive-wedding-e-Card.jpg',
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
                    'unit_price' => 1299.00,
                    'sub_category' => 'Premium',
                    'image_path' => 'images/visiting-cards/executive-matte-finish-card.jpg',
                    'is_active' => true,
                    'sort_order' => 1,
                ],
                [
                    'title' => 'Standard Glossy Business Card',
                    'slug' => 'standard-glossy-business-card',
                    'description' => 'High-quality business card with glossy finish and vibrant color printing, perfect for networking.',
                    'unit_price' => 999.00,
                    'sub_category' => 'Standard',
                    'image_path' => 'images/visiting-cards/standard-glossy-business-card.jpg',
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
            // Flyers (Abhi bhi single image style mein hai)
            'Flyers' => [
                'sub_categories' => ['Standard', 'Premium'],
                'base_unit_price' => 1499.00,
                'image' => 'Folded brochure.jpg'
            ],

            // Banners (Multiple Images)
            'Banners' => [
                'sub_categories' => [
                    'Vinyl' => [
                        'image' => 'images/banners/vinyl-banner.jpg',
                        'price_modifier' => 0
                    ],
                    'Fabric' => [
                        'image' => 'images/banners/fabric-banner.jpg',
                        'price_modifier' => 500
                    ],
                    'Mesh' => [
                        'image' => 'images/banners/mesh-banner.jpg',
                        'price_modifier' => 1000
                    ]
                ],
                'base_unit_price' => 2999.00,
            ],

            // Brochures (Multiple Images)
            'Brochures' => [
                'sub_categories' => [
                    'Bi-fold' => [
                        'image' => 'images/brochures/bi-fold-brochure.jpg',
                        'price_modifier' => 0
                    ],
                    'Tri-fold' => [
                        'image' => 'images/brochures/tri-fold-brochure.jpg',
                        'price_modifier' => 200
                    ],
                    'Z-fold' => [
                        'image' => 'images/brochures/z-fold-brochure.jpg',
                        'price_modifier' => 400
                    ]
                ],
                'base_unit_price' => 2499.00,
            ],

            // Posters (Multiple Images) - ✅ Updated
            'Posters' => [
                'sub_categories' => [
                    'A3' => [
                        'image' => 'images/posters/a3-poster.jpg', // Image 1
                        'price_modifier' => 0
                    ],
                    'A2' => [
                        'image' => 'images/posters/a2-poster.jpg', // Image 2
                        'price_modifier' => 500
                    ],
                    'A1' => [
                        'image' => 'images/posters/a1-poster.jpeg', // Image 3
                        'price_modifier' => 1000
                    ]
                ],
                'base_unit_price' => 1999.00,
            ],
        ];

        // ✅ Updated Loop Logic (Jo dono styles ko handle karega)
        foreach ($otherServices as $serviceTitle => $config) {
            $service = Service::where('title', $serviceTitle)->first();
            if ($service) {
                $counter = 1;

                foreach ($config['sub_categories'] as $key => $value) {

                    // Check: Agar value array hai (Banners/Brochures) ya string (Flyers/Posters)
                    if (is_array($value)) {
                        // Naya Style (Multiple Images)
                        $subCategoryName = $key;
                        $imagePath = $value['image'];
                        $price = $config['base_unit_price'] + $value['price_modifier'];
                    } else {
                        // Purana Style (Single Image)
                        $subCategoryName = $value;
                        $imagePath = $config['image'];
                        $price = $config['base_unit_price'] + ($counter * 200);
                    }

                    $sample = [
                        'service_id' => $service->id,
                        'title' => $subCategoryName . ' ' . $serviceTitle,
                        'slug' => Str::slug($subCategoryName . ' ' . $serviceTitle),
                        'description' => 'Professional ' . strtolower($serviceTitle) . ' in ' . $subCategoryName . ' format with high-quality printing and design.',
                        'unit_price' => $price,
                        'sub_category' => $subCategoryName,
                        'image_path' => $imagePath,
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