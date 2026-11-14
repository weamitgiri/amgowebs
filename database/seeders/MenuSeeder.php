<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // Rebuild the primary header/footer navigation from scratch so renamed or
        // retired items (e.g. the discontinued Server Hosting offering, or
        // "About Us" → "About") never leave stale duplicates behind.
        Menu::whereIn('location', ['header', 'footer'])->delete();

        $header = [
            ['Home', '/'],
            ['About', '/about'],
            ['Services', '/services'],
            ['Industries', '/industries'],
            ['Our Work', '/portfolio'],
            ['Pricing', '/pricing'],
            ['How It Works', '/how-it-works'],
            ['Blog', '/blog'],
            ['Contact', '/contact'],
        ];

        foreach ($header as $i => [$label, $url]) {
            Menu::updateOrCreate(
                ['location' => 'header', 'label' => $label],
                ['url' => $url, 'sort_order' => $i, 'is_active' => true],
            );
        }

        $footer = [
            ['About', '/about'],
            ['Services', '/services'],
            ['Industries', '/industries'],
            ['Our Work', '/portfolio'],
            ['Pricing', '/pricing'],
            ['How It Works', '/how-it-works'],
            ['FAQ', '/faq'],
            ['Blog', '/blog'],
            ['Careers', '/careers'],
            ['Contact', '/contact'],
            ['Privacy Policy', '/privacy'],
            ['Terms of Service', '/terms'],
        ];

        foreach ($footer as $i => [$label, $url]) {
            Menu::updateOrCreate(
                ['location' => 'footer', 'label' => $label],
                ['url' => $url, 'sort_order' => $i, 'is_active' => true],
            );
        }
    }
}
