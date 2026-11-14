<?php

namespace App\Support;

use App\Models\Post;
use Illuminate\Support\Str;

/**
 * Calculates a blog post's SEO score from REAL, checkable on-page factors
 * (never random). Reused by the blog listing and the SEO wizard.
 */
class SeoScorer
{
    /**
     * @return array{score:int, checks:array<int,array{label:string,passed:bool,weight:int,hint:string}>}
     */
    public static function evaluate(Post $post): array
    {
        $seo = $post->seo;
        $title = trim((string) ($seo?->title ?: $post->title));
        $desc = trim((string) ($seo?->description ?: $post->excerpt));
        $focus = trim((string) ($seo?->focus_keyword ?? ''));
        $bodyHtml = (string) $post->body;
        $bodyText = strip_tags($bodyHtml);
        $words = str_word_count($bodyText);
        $titleLen = Str::length($title);
        $descLen = Str::length($desc);
        $focusLower = Str::lower($focus);

        $checks = [
            ['label' => 'URL slug set', 'weight' => 5, 'passed' => filled($post->slug), 'hint' => 'Give the post a clean slug.'],
            ['label' => 'Title length 30–65 chars', 'weight' => 10, 'passed' => $titleLen >= 30 && $titleLen <= 65, 'hint' => 'Keep the title concise but descriptive.'],
            ['label' => 'Meta description 50–160 chars', 'weight' => 15, 'passed' => $descLen >= 50 && $descLen <= 160, 'hint' => 'Write a compelling meta description.'],
            ['label' => 'Focus keyword set', 'weight' => 10, 'passed' => $focus !== '', 'hint' => 'Add a primary keyword in SEO settings.'],
            ['label' => 'Focus keyword in title', 'weight' => 10, 'passed' => $focus !== '' && Str::contains(Str::lower($title), $focusLower), 'hint' => 'Include the focus keyword in the title.'],
            ['label' => 'Focus keyword in content', 'weight' => 10, 'passed' => $focus !== '' && Str::contains(Str::lower($bodyText), $focusLower), 'hint' => 'Use the focus keyword naturally in the body.'],
            ['label' => 'Featured image', 'weight' => 10, 'passed' => filled($post->featured_image), 'hint' => 'Upload a featured image.'],
            ['label' => 'Image ALT text', 'weight' => 5, 'passed' => filled($post->image_alt), 'hint' => 'Describe the image with ALT text.'],
            ['label' => 'Content ≥ 300 words', 'weight' => 15, 'passed' => $words >= 300, 'hint' => 'Aim for genuinely helpful, in-depth content.'],
            ['label' => 'Has an H2 subheading', 'weight' => 10, 'passed' => Str::contains(Str::lower($bodyHtml), '<h2'), 'hint' => 'Structure the article with H2 headings.'],
        ];

        $score = (int) collect($checks)->where('passed', true)->sum('weight');

        return ['score' => $score, 'checks' => $checks];
    }

    public static function score(Post $post): int
    {
        return self::evaluate($post)['score'];
    }
}
