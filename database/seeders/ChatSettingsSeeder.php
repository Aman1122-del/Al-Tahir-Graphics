<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class ChatSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = Setting::first();
        
        if ($settings) {
            $settings->update([
                'chat_email_notifications' => true,
                'chat_admin_email' => 'admin@altahirgraphics.com',
                'chat_smtp_host' => 'smtp.gmail.com',
                'chat_smtp_port' => 587,
                'chat_smtp_username' => env('MAIL_USERNAME', ''),
                'chat_smtp_password' => env('MAIL_PASSWORD', ''),
                'chat_smtp_encryption' => true,
            ]);
        } else {
            Setting::create([
                'site_name' => 'Al-Tahir Graphics',
                'site_email' => 'info@altahirgraphics.com',
                'chat_email_notifications' => true,
                'chat_admin_email' => 'admin@altahirgraphics.com',
                'chat_smtp_host' => 'smtp.gmail.com',
                'chat_smtp_port' => 587,
                'chat_smtp_username' => env('MAIL_USERNAME', ''),
                'chat_smtp_password' => env('MAIL_PASSWORD', ''),
                'chat_smtp_encryption' => true,
            ]);
        }
    }
}