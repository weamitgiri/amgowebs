<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        // NOTE: "Webveo" is a placeholder brand name. Change it (and the contact
        // details / socials below) in Admin → Settings — it flows site-wide.
        $brand = env('SITE_BRAND_NAME', 'Webveo');

        // Retire the discontinued WhatsApp contact field (replaced by Telegram).
        Setting::where('key', 'whatsapp')->delete();

        $settings = [
            // group, key, value, type
            ['general', 'site_name', $brand, 'text'],
            ['general', 'site_tagline', 'Professional Websites at Affordable Prices', 'text'],
            ['general', 'logo', '', 'image'],
            ['general', 'favicon', '', 'image'],
            ['general', 'maintenance_mode', '0', 'boolean'],
            ['general', 'timezone', 'UTC', 'text'],
            ['general', 'default_currency', env('SITE_DEFAULT_CURRENCY', 'USD'), 'text'],
            ['general', 'team_size', '50+', 'text'],

            ['contact', 'contact_email', env('SITE_SUPPORT_EMAIL', 'hello@webveo.example'), 'email'],
            ['contact', 'contact_phone', '+1 (000) 000-0000', 'text'],
            ['contact', 'telegram', env('SITE_TELEGRAM', 'webveo'), 'text'],
            ['contact', 'address', 'Remote-first — serving clients worldwide', 'textarea'],

            ['social', 'social_facebook', '', 'url'],
            ['social', 'social_instagram', '', 'url'],
            ['social', 'social_linkedin', '', 'url'],
            ['social', 'social_x', '', 'url'],
            ['social', 'social_youtube', '', 'url'],

            ['seo', 'seo_default_title', $brand.' — Professional Websites at Affordable Prices', 'text'],
            ['seo', 'seo_default_description', 'Affordable, professionally designed websites for small businesses — fixed one-time pricing from $199, fast delivery, SEO-ready, and full ownership. Serving businesses worldwide, in every country.', 'textarea'],
            ['seo', 'seo_default_og_image', '', 'image'],
            ['seo', 'seo_organization_name', $brand, 'text'],
            ['seo', 'seo_robots_default', 'index, follow', 'text'],

            ['analytics', 'google_analytics_id', '', 'text'],
            ['analytics', 'google_tag_manager_id', '', 'text'],

            ['mail', 'mail_from_name', $brand, 'text'],
            ['mail', 'inquiry_notify_email', env('SITE_SUPPORT_EMAIL', 'hello@webveo.example'), 'email'],
        ];

        foreach ($settings as [$group, $key, $value, $type]) {
            Setting::updateOrCreate(['key' => $key], ['group' => $group, 'value' => $value, 'type' => $type]);
        }
    }
}
