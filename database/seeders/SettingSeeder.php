<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'general' => [
                'site_name' => 'NOKSHI',
                'site_tagline' => 'Wear the story.',
                'site_tagline_bn' => 'গল্প পরো।',
                'site_description' => 'NOKSHI is a Dhaka-born clothing label blending heritage craft with modern silhouettes — sarees, panjabis, kurtis and everyday essentials made to be lived in.',
                'currency_code' => 'BDT',
                'currency_symbol' => '৳',
                'announcement_text' => 'Free delivery inside Dhaka on orders over ৳2,000 · Eid collection now live',
                'announcement_active' => '1',
                'free_shipping_threshold' => '2000',
                'low_stock_threshold' => '5',
                'products_per_page' => '12',
                'reviews_auto_approve' => '0',
                'guest_checkout' => '1',
                'maintenance_mode' => '0',
            ],
            'contact' => [
                'contact_email' => 'hello@nokshi.com.bd',
                'contact_phone' => '+880 1711-000000',
                'contact_whatsapp' => '8801711000000',
                'contact_address' => 'House 42, Road 11, Banani, Dhaka 1213, Bangladesh',
                'business_hours' => 'Sat – Thu, 10:00 AM – 9:00 PM',
                'map_embed' => '',
            ],
            'social' => [
                'social_facebook' => 'https://facebook.com/nokshi',
                'social_instagram' => 'https://instagram.com/nokshi',
                'social_youtube' => 'https://youtube.com/@nokshi',
                'social_tiktok' => 'https://tiktok.com/@nokshi',
                'social_pinterest' => '',
            ],
            'seo' => [
                'meta_title' => 'NOKSHI — Clothing from Dhaka, made with intention',
                'meta_description' => 'Shop sarees, panjabis, kurtis, streetwear and essentials. Handpicked fabrics, honest prices, delivered across Bangladesh.',
                'meta_keywords' => 'nokshi, clothing bangladesh, saree, panjabi, kurti, fashion dhaka',
                'og_image' => '',
                'google_analytics' => '',
                'facebook_pixel' => '',
            ],
            'payment' => [
                'cod_enabled' => '1',
                'cod_fee' => '0',
                'sslcommerz_enabled' => '1',
                'sslcommerz_store_id' => '',
                'sslcommerz_store_password' => '',
                'sslcommerz_sandbox' => '1',
                'bkash_number' => '01711-000000',
            ],
            'appearance' => [
                'logo' => '',
                'logo_dark' => '',
                'favicon' => '',
                'primary_color' => '#5B1B4F',
                'accent_color' => '#F5C24B',
                'hero_style' => 'editorial',
                'show_flash_sale' => '1',
                'show_testimonials' => '1',
                'show_blog' => '1',
                'show_instagram' => '1',
            ],
        ];

        foreach ($settings as $group => $items) {
            foreach ($items as $key => $value) {
                Setting::firstOrCreate(['key' => $key], ['group' => $group, 'value' => $value]);
            }
        }
    }
}
