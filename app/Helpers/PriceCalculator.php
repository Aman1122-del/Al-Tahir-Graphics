<?php

namespace App\Helpers;

class PriceCalculator
{
    /**
     * Calculate the final price based on base price and selected variants
     */
    public static function calculatePrice(float $basePrice, array $variants = []): float
    {
        $multiplier = 1.0;
        
        // Size multipliers
        if (isset($variants['size'])) {
            switch ($variants['size']) {
                case 'large':
                    $multiplier += 0.2;
                    break;
                case 'extra-large':
                    $multiplier += 0.4;
                    break;
                case 'standard':
                default:
                    // No change
                    break;
            }
        }
        
        // Paper type multipliers
        if (isset($variants['paper_type'])) {
            switch ($variants['paper_type']) {
                case 'premium':
                    $multiplier += 0.3;
                    break;
                case 'luxury':
                    $multiplier += 0.5;
                    break;
                case 'standard':
                default:
                    // No change
                    break;
            }
        }
        
        // Finish multipliers
        if (isset($variants['finish'])) {
            switch ($variants['finish']) {
                case 'glossy':
                    $multiplier += 0.1;
                    break;
                case 'satin':
                    $multiplier += 0.2;
                    break;
                case 'matte':
                default:
                    // No change
                    break;
            }
        }
        
        return $basePrice * $multiplier;
    }
    
    /**
     * Get formatted price string
     */
    public static function formatPrice(float $price): string
    {
        return 'PKR ' . number_format($price, 0);
    }
    
    /**
     * Get variant display name
     */
    public static function getVariantDisplayName(string $type, string $value): string
    {
        $displayNames = [
            'size' => [
                'standard' => 'Standard',
                'large' => 'Large',
                'extra-large' => 'Extra Large',
            ],
            'paper_type' => [
                'standard' => 'Standard',
                'premium' => 'Premium',
                'luxury' => 'Luxury',
            ],
            'finish' => [
                'matte' => 'Matte',
                'glossy' => 'Glossy',
                'satin' => 'Satin',
            ],
        ];
        
        return $displayNames[$type][$value] ?? ucfirst($value);
    }
}
