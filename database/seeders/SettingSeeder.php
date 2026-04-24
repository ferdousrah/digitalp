<?php
namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'general', 'key' => 'site_name', 'value' => 'Digital Support', 'type' => 'text'],
            ['group' => 'general', 'key' => 'site_tagline', 'value' => 'Your Digital Products Partner', 'type' => 'text'],
            ['group' => 'general', 'key' => 'site_description', 'value' => 'Digital Support offers a wide range of computer accessories, laptops, printers, and digital products.', 'type' => 'textarea'],
            ['group' => 'branding', 'key' => 'site_logo', 'value' => null, 'type' => 'image'],
            ['group' => 'branding', 'key' => 'site_favicon', 'value' => null, 'type' => 'image'],
            ['group' => 'contact', 'key' => 'contact_email', 'value' => 'info@digitalsupport.com', 'type' => 'text'],
            ['group' => 'contact', 'key' => 'contact_phone', 'value' => '+1 234 567 890', 'type' => 'text'],
            ['group' => 'contact', 'key' => 'contact_address', 'value' => '123 Tech Street, Digital City, DC 12345', 'type' => 'textarea'],
            ['group' => 'contact', 'key' => 'contact_whatsapp', 'value' => '+1234567890', 'type' => 'text'],
            ['group' => 'social', 'key' => 'social_facebook', 'value' => '#', 'type' => 'text'],
            ['group' => 'social', 'key' => 'social_twitter', 'value' => '#', 'type' => 'text'],
            ['group' => 'social', 'key' => 'social_instagram', 'value' => '#', 'type' => 'text'],
            ['group' => 'social', 'key' => 'social_linkedin', 'value' => '#', 'type' => 'text'],
            ['group' => 'seo', 'key' => 'meta_title', 'value' => 'Digital Support - Computer Accessories, Laptops & Printers', 'type' => 'text'],
            ['group' => 'seo', 'key' => 'meta_description', 'value' => 'Shop the latest computer accessories, laptops, printers, and digital products at Digital Support. Quality products, great prices.', 'type' => 'textarea'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
