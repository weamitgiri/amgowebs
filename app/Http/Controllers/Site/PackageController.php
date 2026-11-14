<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Page;
use App\Models\Setting;
use App\Services\SeoService;
use Illuminate\View\View;

class PackageController extends Controller
{
    public function index(SeoService $seo): View
    {
        $packages = Package::published()->ordered()->with('features')->get();
        $page = Page::where('slug', 'pricing')->first();

        return view('site.pricing', [
            'packages' => $packages,
            'seo' => $seo->resolve($page),
        ]);
    }

    public function show(string $slug, SeoService $seo): View
    {
        $package = Package::published()->where('slug', $slug)->with('features')->firstOrFail();

        $brand = Setting::get('seo_organization_name', Setting::get('site_name', config('app.name')));

        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'serviceType' => 'Website design and development',
            'name' => $package->name,
            'description' => $package->short_description,
            'areaServed' => 'Worldwide',
            'provider' => ['@type' => 'Organization', 'name' => $brand, 'url' => url('/')],
        ];
        if (! $package->is_custom_pricing && $package->price_usd) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => (string) (int) $package->price_usd,
                'priceCurrency' => 'USD',
                'availability' => 'https://schema.org/InStock',
                'url' => route('packages.show', $package->slug),
            ];
        }

        return view('site.package-show', [
            'package' => $package,
            'related' => Package::published()->where('id', '!=', $package->id)->ordered()->limit(3)->get(),
            'seo' => $seo->resolve($package, ['schema' => $schema]),
        ]);
    }
}
