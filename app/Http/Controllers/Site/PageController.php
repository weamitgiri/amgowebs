<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Page;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    /**
     * Render a CMS page by its slug (used as the catch-all fallback route).
     */
    public function show(Request $request, SeoService $seo): View
    {
        $slug = trim($request->path(), '/');

        $page = Page::published()->where('slug', $slug)->with('sections')->firstOrFail();

        $types = $page->sections->pluck('type');

        $data = [
            'page' => $page,
            'seo' => $seo->resolve($page),
            'packages' => ($slug === 'services' || $types->contains('pricing'))
                ? Package::published()->ordered()->with('includedFeatures')->get()
                : collect(),
            'posts' => $types->contains('blog_list')
                ? \App\Models\Post::published()->latest('published_at')->limit(3)->get()
                : collect(),
        ];

        $view = 'site.pages.'.$slug;

        return view()->exists($view) ? view($view, $data) : view('site.pages.default', $data);
    }
}
