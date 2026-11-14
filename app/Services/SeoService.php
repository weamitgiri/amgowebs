<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SeoService
{
    /**
     * Resolve SEO metadata for a page, falling back to the model and global defaults.
     *
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    public function resolve(?Model $model = null, array $overrides = []): array
    {
        $seo = ($model && method_exists($model, 'seo')) ? $model->seo : null;
        $siteName = Setting::get('site_name', config('app.name'));

        $title = $overrides['title']
            ?? $seo?->title
            ?? ($model->title ?? null)
            ?? Setting::get('seo_default_title', $siteName);

        // Append the brand name if the title doesn't already include it.
        if ($siteName && ! Str::contains($title, $siteName)) {
            $title = "{$title} | {$siteName}";
        }

        $description = $overrides['description']
            ?? $seo?->description
            ?? ($model->excerpt ?? null)
            ?? Setting::get('seo_default_description');

        $canonical = $overrides['canonical']
            ?? $seo?->canonical_url
            ?? url()->current();

        $robots = $overrides['robots']
            ?? $seo?->robots
            ?? Setting::get('seo_robots_default', 'index, follow');

        $ogImage = $overrides['og_image']
            ?? $seo?->og_image
            ?? Setting::get('seo_default_og_image');

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => $robots,
            'og_title' => $seo?->og_title ?? $title,
            'og_description' => $seo?->og_description ?? $description,
            'og_image' => $ogImage,
            'og_type' => $overrides['og_type'] ?? 'website',
            'schema' => $overrides['schema'] ?? $seo?->schema_json,
        ];
    }
}
