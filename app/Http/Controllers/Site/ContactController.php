<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Page;
use App\Services\SeoService;
use App\Support\LeadOptions;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContactController extends Controller
{
    public function index(Request $request, SeoService $seo): View
    {
        $page = Page::where('slug', 'contact')->first();
        $packages = Package::published()->ordered()->get();

        return view('site.contact', [
            'packages' => $packages,
            'selectedPackageId' => optional($packages->firstWhere('slug', $request->query('package')))->id,
            'countries' => LeadOptions::countries(),
            'websiteTypes' => LeadOptions::websiteTypes(),
            'pageOptions' => LeadOptions::pageOptions(),
            'budgets' => LeadOptions::budgets(),
            'seo' => $seo->resolve($page),
        ]);
    }
}
