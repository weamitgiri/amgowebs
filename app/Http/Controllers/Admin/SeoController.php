<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Package;
use App\Models\Page;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SeoController extends Controller
{
    public function index(): View
    {
        $issues = $this->audit();

        return view('admin.seo.index', [
            'issues' => $issues,
            'severityCounts' => collect($issues)->groupBy('severity')->map->count(),
            'redirects' => Redirect::latest()->get(),
            'globals' => [
                'Default title' => Setting::get('seo_default_title'),
                'Default description' => Setting::get('seo_default_description'),
                'Organization' => Setting::get('seo_organization_name'),
                'Default robots' => Setting::get('seo_robots_default'),
            ],
        ]);
    }

    /**
     * Audit indexable content for SEO issues.
     *
     * @return array<int, array{severity:string,type:string,message:string,item:string,link:?string}>
     */
    private function audit(): array
    {
        $issues = [];
        $titles = [];
        $descriptions = [];

        $add = function (string $sev, string $type, string $msg, string $item, ?string $link = null) use (&$issues) {
            $issues[] = ['severity' => $sev, 'type' => $type, 'message' => $msg, 'item' => $item, 'link' => $link];
        };

        // Pages
        foreach (Page::with('seo')->where('status', 'published')->get() as $p) {
            $seoTitle = $p->seo?->title ?: $p->title;
            $seoDesc = $p->seo?->description ?: $p->excerpt;
            if (blank($p->seo?->title)) {
                $add('low', 'Page', 'No custom SEO title (using page title)', $p->title);
            }
            if (blank($seoDesc)) {
                $add('high', 'Page', 'Missing meta description', $p->title);
            }
            if ($p->seo && str_contains((string) $p->seo->robots, 'noindex')) {
                $add('info', 'Page', 'Set to noindex', $p->title);
            }
            if (! $p->is_in_sitemap && $p->slug !== 'home') {
                $add('info', 'Page', 'Excluded from sitemap', $p->title);
            }
            $titles[] = [$seoTitle, 'Page: '.$p->title];
            $descriptions[] = [$seoDesc, 'Page: '.$p->title];
        }

        // Packages
        foreach (Package::with('seo')->published()->get() as $p) {
            $seoTitle = $p->seo?->title ?: $p->name;
            $seoDesc = $p->seo?->description ?: $p->short_description;
            if (blank($seoDesc)) {
                $add('high', 'Package', 'Missing meta description', $p->name, route('admin.packages.edit', $p));
            }
            $titles[] = [$seoTitle, 'Package: '.$p->name];
            $descriptions[] = [$seoDesc, 'Package: '.$p->name];
        }

        // Posts
        foreach (Post::with('seo')->where('status', 'published')->get() as $p) {
            $seoDesc = $p->seo?->description ?: $p->excerpt;
            if (blank($seoDesc)) {
                $add('high', 'Post', 'Missing meta description', $p->title, route('admin.posts.edit', $p));
            }
            $titles[] = [$p->seo?->title ?: $p->title, 'Post: '.$p->title];
            $descriptions[] = [$seoDesc, 'Post: '.$p->title];
        }

        // Duplicate titles / descriptions
        $this->findDuplicates($titles, fn ($label) => $add('medium', 'Duplicate', 'Duplicate SEO title', $label));
        $this->findDuplicates($descriptions, fn ($label) => $add('medium', 'Duplicate', 'Duplicate meta description', $label));

        // Sort by severity weight
        $weight = ['high' => 0, 'medium' => 1, 'low' => 2, 'info' => 3];
        usort($issues, fn ($a, $b) => $weight[$a['severity']] <=> $weight[$b['severity']]);

        return $issues;
    }

    private function findDuplicates(array $pairs, callable $onDup): void
    {
        $seen = [];
        foreach ($pairs as [$value, $label]) {
            if (blank($value)) {
                continue;
            }
            $key = mb_strtolower(trim($value));
            if (isset($seen[$key])) {
                $onDup($label);
            }
            $seen[$key] = true;
        }
    }

    public function storeRedirect(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'from_path' => ['required', 'string', 'max:255', 'unique:redirects,from_path'],
            'to_path' => ['required', 'string', 'max:255'],
            'status_code' => ['required', 'in:301,302'],
        ]);
        $data['from_path'] = '/'.ltrim($data['from_path'], '/');
        Redirect::create($data + ['is_active' => true]);
        ActivityLog::record('redirect.created', "Redirect {$data['from_path']} → {$data['to_path']}");

        return back()->with('status', 'Redirect added.');
    }

    public function destroyRedirect(Redirect $redirect): RedirectResponse
    {
        $redirect->delete();

        return back()->with('status', 'Redirect removed.');
    }
}
