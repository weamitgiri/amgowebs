<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\JobOpening;
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CareerController extends Controller
{
    public function index(): View
    {
        $team = Setting::get('team_size', '50+');

        return view('site.careers.index', [
            'jobs' => JobOpening::published()->ordered()->get(),
            'teamSize' => $team,
            'seo' => [
                'title' => 'Careers — Join Our Remote Web Design Team',
                'description' => "We're a remote-first web design studio of {$team} developers and designers, hiring worldwide. Explore our open roles and apply.",
                'focus_keyword' => 'web design agency careers',
            ],
        ]);
    }

    public function show(string $slug): View
    {
        $job = JobOpening::published()->where('slug', $slug)->firstOrFail();

        $plainDesc = trim(implode("\n\n", array_filter([
            $job->summary,
            $job->description,
            $job->responsibilities ? "Responsibilities:\n".$job->responsibilities : null,
            $job->requirements ? "Requirements:\n".$job->requirements : null,
        ])));

        $org = Setting::get('seo_organization_name') ?: Setting::get('site_name', config('app.name'));

        $schema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $job->title,
            'description' => nl2br(e($plainDesc)),
            'datePosted' => optional($job->posted_at)->toDateString() ?: $job->created_at->toDateString(),
            'validThrough' => optional($job->closes_at)->toDateString() ?: null,
            'employmentType' => JobOpening::SCHEMA_TYPES[$job->employment_type] ?? 'OTHER',
            'hiringOrganization' => array_filter([
                '@type' => 'Organization',
                'name' => $org,
                'sameAs' => url('/'),
                'logo' => Setting::get('logo') ?: null,
            ]),
            'jobLocationType' => $job->is_remote ? 'TELECOMMUTE' : null,
            'applicantLocationRequirements' => $job->is_remote ? ['@type' => 'Country', 'name' => 'Worldwide'] : null,
            'jobLocation' => $job->is_remote ? null : [
                '@type' => 'Place',
                'address' => ['@type' => 'PostalAddress', 'addressLocality' => $job->location],
            ],
        ], fn ($v) => ! is_null($v) && $v !== '');

        return view('site.careers.show', [
            'job' => $job,
            'seo' => [
                'title' => $job->title.' — Careers',
                'description' => $job->summary ?: Str::limit($plainDesc, 155),
                'og_type' => 'article',
                'schema' => $schema,
            ],
        ]);
    }
}
