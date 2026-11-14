<?php

namespace Database\Seeders;

use App\Models\JobOpening;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class JobSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->jobs() as $i => $data) {
            JobOpening::updateOrCreate(
                ['slug' => Str::slug($data['title'])],
                array_merge($data, [
                    'is_published' => true,
                    'is_remote' => true,
                    'location' => $data['location'] ?? 'Remote — Worldwide',
                    'sort_order' => $i,
                    'posted_at' => now()->subDays(($i + 1) * 3),
                ]),
            );
        }
    }

    private function jobs(): array
    {
        return [
            [
                'title' => 'Senior Laravel / PHP Developer',
                'department' => 'Engineering',
                'employment_type' => 'full-time',
                'experience_level' => 'Senior',
                'summary' => 'Build and ship robust, well-tested Laravel applications for clients worldwide, and help shape how we work.',
                'description' => "We're looking for an experienced Laravel developer to join our remote engineering team. You'll own features end to end — from database design to clean, tested code — and mentor others as we grow.",
                'responsibilities' => "Design, build and maintain Laravel applications and APIs\nWrite clean, well-tested, maintainable code\nReview pull requests and mentor mid-level developers\nCollaborate with designers and project managers on scope and delivery\nHelp improve our engineering standards and tooling",
                'requirements' => "4+ years building production PHP/Laravel applications\nStrong MySQL and database design skills\nComfortable with Git, testing (PHPUnit/Pest) and CI\nGood written English and clear communication\nSelf-driven and reliable in a remote team",
            ],
            [
                'title' => 'Frontend Developer (React / Vue)',
                'department' => 'Engineering',
                'employment_type' => 'full-time',
                'experience_level' => 'Mid',
                'summary' => 'Turn designs into fast, accessible, pixel-accurate interfaces that work beautifully on every device.',
                'description' => "You'll build responsive, high-performance front-ends for marketing sites and web apps, with a strong eye for detail and accessibility.",
                'responsibilities' => "Build responsive UIs from design files\nWrite reusable components in React or Vue\nOptimise for performance and Core Web Vitals\nEnsure accessibility and cross-browser support\nWork closely with designers and backend developers",
                'requirements' => "3+ years of modern JavaScript (React or Vue)\nStrong HTML and CSS, including responsive design\nAn eye for detail and smooth interactions\nFamiliarity with performance and accessibility best practices\nClear communication in a remote team",
            ],
            [
                'title' => 'WordPress Developer',
                'department' => 'Engineering',
                'employment_type' => 'contract',
                'experience_level' => 'Mid',
                'summary' => 'Build and customise fast, secure WordPress sites and themes for small-business clients.',
                'description' => "We take on a steady stream of WordPress projects and need a dependable developer to build custom themes and maintain existing sites.",
                'responsibilities' => "Build custom WordPress themes and lightweight plugins\nMigrate and redesign existing WordPress sites\nOptimise performance, security and SEO basics\nTroubleshoot and maintain client sites",
                'requirements' => "3+ years of hands-on WordPress development\nComfortable with PHP, HTML, CSS and JavaScript\nExperience with performance and security hardening\nReliable, communicative and deadline-focused",
            ],
            [
                'title' => 'UI/UX Designer',
                'department' => 'Design',
                'employment_type' => 'full-time',
                'experience_level' => 'Mid',
                'summary' => 'Design clean, modern, conversion-focused websites that are a joy to use.',
                'description' => "You'll craft the look and feel of client websites — from wireframes to polished, responsive designs that build trust and drive action.",
                'responsibilities' => "Create wireframes, mockups and design systems\nDesign responsive, accessible, conversion-focused layouts\nCollaborate with developers to ship pixel-accurate work\nIncorporate client feedback efficiently",
                'requirements' => "3+ years designing websites or web apps\nStrong portfolio of modern, responsive work\nProficiency in Figma (or similar)\nUnderstanding of UX, accessibility and conversion principles",
            ],
            [
                'title' => 'SEO Specialist',
                'department' => 'Marketing',
                'employment_type' => 'part-time',
                'experience_level' => 'Mid',
                'summary' => 'Help our clients get found on Google with solid on-page and technical SEO.',
                'description' => "You'll handle on-page and technical SEO for client sites and advise on content that ranks — honestly, with realistic expectations.",
                'responsibilities' => "Run on-page and technical SEO audits\nImplement titles, meta, schema and internal linking\nAdvise on keyword-focused, people-first content\nTrack performance in Search Console and analytics",
                'requirements' => "2+ years of hands-on SEO experience\nStrong grasp of on-page and technical SEO\nFamiliarity with Search Console, analytics and schema\nClear, honest communication — no black-hat tactics",
            ],
        ];
    }
}
