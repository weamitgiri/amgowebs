<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Page;
use App\Models\Post;
use App\Services\SeoService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(SeoService $seo): View
    {
        $packages = Package::published()->ordered()->with('includedFeatures')->get();
        $posts = Post::published()->latest('published_at')->limit(3)->get();
        $page = Page::where('slug', 'home')->with('sections')->first() ?? new Page();

        return view('site.home', [
            'page' => $page,
            'packages' => $packages,
            'posts' => $posts,
            'teamSize' => \App\Models\Setting::get('team_size', '50+'),
            'seo' => $seo->resolve($page->exists ? $page : null),
        ]);
    }
}
