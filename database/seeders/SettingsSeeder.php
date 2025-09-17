<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                'key' => 'site_name',
                'value' => 'Al-Tahir Graphics',
                'type' => 'string',
                'description' => 'The name of the website',
            ],
            [
                'key' => 'site_email',
                'value' => 'admin@altahirgraphics.com',
                'type' => 'string',
                'description' => 'The main email address for the website',
            ],
            [
                'key' => 'site_phone',
                'value' => '+92 300 1234567',
                'type' => 'string',
                'description' => 'The main phone number for the website',
            ],
            [
                'key' => 'site_address',
                'value' => '123 Main Street, Karachi, Pakistan',
                'type' => 'text',
                'description' => 'The business address',
            ],
            [
                'key' => 'currency',
                'value' => 'PKR',
                'type' => 'string',
                'description' => 'The default currency for the website',
            ],
            [
                'key' => 'timezone',
                'value' => 'Asia/Karachi',
                'type' => 'string',
                'description' => 'The default timezone for the website',
            ],
            [
                'key' => 'maintenance_mode',
                'value' => '0',
                'type' => 'boolean',
                'description' => 'Enable or disable maintenance mode',
            ],
            [
                'key' => 'registration_enabled',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Allow or disallow user registration',
            ],
            [
                'key' => 'email_notifications',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable or disable email notifications',
            ],
            [
                'key' => 'order_notifications',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable or disable order notifications',
            ],
            [
                'key' => 'quote_notifications',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable or disable quote notifications',
            ],
            [
                'key' => 'invoice_notifications',
                'value' => '1',
                'type' => 'boolean',
                'description' => 'Enable or disable invoice notifications',
            ],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}