<?php

namespace Database\Seeders;

use App\Models\Package;
use Illuminate\Database\Seeder;

class PackageSeeder extends Seeder
{
    public function run(): void
    {
        $hostingNote = 'Hosting & domain (kept in your name — we help you set it up)';

        $packages = [
            [
                'name' => 'Starter Website',
                'slug' => 'starter-website',
                'price_usd' => 199,
                'short_description' => 'A professional 5-page website to launch your business online — affordably and fast.',
                'description' => 'The Starter Website is the quickest, most affordable way to get a credible online presence. Perfect for new businesses, freelancers and local services that need a clean, fast, mobile-friendly site at a fixed price — with everything set up right the first time.',
                'pages_included' => 5,
                'delivery_days' => 7,
                'support_months' => 0,
                'revisions' => 2,
                'icon' => 'rocket',
                'cta_label' => 'Get Started',
                'is_featured' => false,
                'sort_order' => 1,
                'included' => [
                    'Up to 5 professionally designed pages',
                    'Mobile-first responsive design',
                    'Contact form with spam protection',
                    'On-page SEO basics (titles, meta, sitemap)',
                    'Fast, Core Web Vitals-friendly build',
                    'Full ownership of your website & files',
                ],
                'excluded' => [$hostingNote, 'Blog / articles module', 'E-commerce / online payments'],
                'addons' => [['Extra page', 25], ['Logo design', 49], ['Blog setup', 79]],
            ],
            [
                'name' => 'Business Website',
                'slug' => 'business-website',
                'price_usd' => 299,
                'short_description' => 'A 10-page business website with inquiry forms and 3 months of free support.',
                'description' => 'The Business Website is built for growing companies that need more room, stronger lead capture and ongoing help. You get up to 10 pages, inquiry forms wired to your inbox, and three months of free technical support after launch — a complete, conversion-focused presence.',
                'pages_included' => 10,
                'delivery_days' => 12,
                'support_months' => 3,
                'revisions' => 3,
                'icon' => 'bolt',
                'cta_label' => 'Get Started',
                'is_featured' => true,
                'sort_order' => 2,
                'included' => [
                    'Up to 10 professionally designed pages',
                    'Mobile-first responsive design',
                    'Contact & inquiry forms wired to your inbox',
                    'On-page SEO setup + structured data',
                    'Google-ready performance & analytics hook-up',
                    '3 months of free technical support',
                    'Full ownership of your website & files',
                ],
                'excluded' => [$hostingNote, 'E-commerce / online payments'],
                'addons' => [['Extra page', 25], ['Blog setup', 79], ['Logo design', 49], ['Copywriting (per page)', 35]],
            ],
            [
                'name' => 'Premium Website',
                'slug' => 'premium-website',
                'price_usd' => 599,
                'short_description' => 'A 20-page premium website with advanced UI/UX, a blog and 6 months of support.',
                'description' => 'The Premium Website delivers an advanced, custom-designed site with up to 20 pages, a built-in blog, enhanced forms and six months of free technical support. Ideal for established businesses that want a content-rich, standout presence engineered to attract and convert organic traffic.',
                'pages_included' => 20,
                'delivery_days' => 21,
                'support_months' => 6,
                'revisions' => 5,
                'icon' => 'star',
                'cta_label' => 'Get Started',
                'is_featured' => false,
                'sort_order' => 3,
                'included' => [
                    'Up to 20 pages with advanced UI/UX design',
                    'Blog / articles module included',
                    'Enhanced contact & inquiry forms',
                    'Advanced on-page SEO + schema markup',
                    'Performance tuning for Core Web Vitals',
                    '6 months of free technical support',
                    'Full ownership of your website & files',
                ],
                'excluded' => [$hostingNote, 'Online store / payment gateway (quoted as custom)'],
                'addons' => [['Extra page', 25], ['Copywriting (per page)', 35], ['Monthly SEO care', 149]],
            ],
            [
                'name' => 'Custom Website',
                'slug' => 'custom-website',
                'price_usd' => null,
                'is_custom_pricing' => true,
                'short_description' => 'E-commerce, advanced business sites and bespoke projects — scoped and quoted to fit.',
                'description' => 'For online stores, advanced business websites, web apps and anything that doesn\'t fit a standard package. Share your requirements and we\'ll prepare a tailored scope, timeline and fixed quote — no obligation.',
                'pages_included' => null,
                'delivery_days' => null,
                'support_months' => null,
                'revisions' => null,
                'icon' => 'brush',
                'cta_label' => 'Request a Quote',
                'is_featured' => false,
                'sort_order' => 4,
                'included' => [
                    'E-commerce & online stores',
                    'Advanced business websites & web apps',
                    'Custom functionality & integrations',
                    'Tailored scope, timeline and fixed quote',
                    'Full ownership of your website & files',
                ],
                'excluded' => [],
                'addons' => [],
            ],
        ];

        foreach ($packages as $data) {
            $included = $data['included'];
            $excluded = $data['excluded'];
            $addons = $data['addons'];
            unset($data['included'], $data['excluded'], $data['addons']);

            $package = Package::updateOrCreate(['slug' => $data['slug']], array_merge([
                'is_custom_pricing' => false,
                'status' => 'published',
                'is_active' => true,
            ], $data));

            $package->features()->delete();
            $order = 0;
            foreach ($included as $label) {
                $package->features()->create(['label' => $label, 'type' => 'included', 'sort_order' => $order++]);
            }
            foreach ($excluded as $label) {
                $package->features()->create(['label' => $label, 'type' => 'excluded', 'sort_order' => $order++]);
            }
            foreach ($addons as [$label, $price]) {
                $package->features()->create(['label' => $label, 'type' => 'addon', 'addon_price' => $price, 'sort_order' => $order++]);
            }

            // Brand-free SEO (SeoService appends the site name automatically).
            $package->seo()->updateOrCreate([], [
                'title' => "{$package->name} — {$package->price_label} Website Package",
                'description' => $package->short_description,
            ]);
        }
    }
}
