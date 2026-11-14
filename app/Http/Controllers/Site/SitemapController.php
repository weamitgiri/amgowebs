<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Package;
use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    /**
     * Dynamic XML sitemap — only canonical, published, indexable content.
     */
    public function index(): Response
    {
        $urls = [];

        // Home
        $urls[] = ['loc' => url('/'), 'priority' => '1.0'];

        // Published, indexable CMS pages (excluding legal/noindex if marked)
        foreach (Page::published()->where('is_in_sitemap', true)->get() as $page) {
            if ($page->slug === 'home') {
                continue;
            }
            // Respect a per-page noindex robots directive.
            if ($page->seo && str_contains((string) $page->seo->robots, 'noindex')) {
                continue;
            }
            $urls[] = ['loc' => url('/'.$page->slug), 'priority' => '0.8', 'lastmod' => $page->updated_at];
        }

        // Published packages
        foreach (Package::published()->get() as $package) {
            $urls[] = ['loc' => route('packages.show', $package->slug), 'priority' => '0.8', 'lastmod' => $package->updated_at];
        }

        // Blog index + categories + posts
        $urls[] = ['loc' => route('blog.index'), 'priority' => '0.7'];
        foreach (Category::has('posts')->get() as $category) {
            $urls[] = ['loc' => route('blog.category', $category->slug), 'priority' => '0.6'];
        }
        foreach (Post::published()->get() as $post) {
            if ($post->seo && str_contains((string) $post->seo->robots, 'noindex')) {
                continue;
            }
            $urls[] = ['loc' => route('blog.show', $post->slug), 'priority' => '0.7', 'lastmod' => $post->updated_at];
        }

        // Careers index + published job openings
        $urls[] = ['loc' => route('careers.index'), 'priority' => '0.6'];
        foreach (\App\Models\JobOpening::published()->get() as $job) {
            $urls[] = ['loc' => route('careers.show', $job->slug), 'priority' => '0.6', 'lastmod' => $job->updated_at];
        }

        // De-duplicate by URL (e.g. the Blog CMS page and the blog index route),
        // keeping the first (higher-priority) occurrence.
        $urls = collect($urls)->unique('loc')->values()->all();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";
        foreach ($urls as $u) {
            $xml .= '  <url><loc>'.e($u['loc']).'</loc>';
            if (! empty($u['lastmod'])) {
                $xml .= '<lastmod>'.$u['lastmod']->toAtomString().'</lastmod>';
            }
            $xml .= '<priority>'.($u['priority'] ?? '0.5').'</priority></url>'."\n";
        }
        $xml .= '</urlset>'."\n";

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    /**
     * Dynamic robots.txt.
     */
    public function robots(): Response
    {
        $maintenance = (bool) Setting::get('maintenance_mode', false);

        $lines = ['User-agent: *'];
        if ($maintenance) {
            $lines[] = 'Disallow: /';
        } else {
            $lines[] = 'Disallow: /admin';
            $lines[] = 'Disallow: /thank-you';
            $lines[] = 'Allow: /';
            $lines[] = '';
            $lines[] = 'Sitemap: '.url('/sitemap.xml');
        }

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain']);
    }
}
