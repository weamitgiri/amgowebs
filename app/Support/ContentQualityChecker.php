<?php

namespace App\Support;

use App\Models\Post;
use Illuminate\Support\Str;

/**
 * Automated content review for a blog post. Every check is derived from the
 * actual content — results feed the editor's "Quality" panel.
 */
class ContentQualityChecker
{
    /**
     * @return array{score:int, issues:list<array{label:string,ok:bool,level:string}>, broken:list<string>}
     */
    public static function check(Post $post): array
    {
        $html = (string) $post->body;
        $text = trim(preg_replace('/\s+/', ' ', strip_tags($html)));
        $words = $text === '' ? 0 : str_word_count($text);

        // Intro = meaningful text before the first H2.
        $beforeH2 = preg_split('/<h2\b/i', $html)[0] ?? $html;
        $introWords = str_word_count(strip_tags($beforeH2));

        // Longest paragraph (word count).
        $longestPara = 0;
        if (preg_match_all('/<p\b[^>]*>(.*?)<\/p>/is', $html, $m)) {
            foreach ($m[1] as $para) {
                $longestPara = max($longestPara, str_word_count(strip_tags($para)));
            }
        }
        $paraCount = preg_match_all('/<p\b/i', $html);

        $broken = self::brokenInternalLinks($html);
        $hasInternalLink = (bool) preg_match('#href=["\']/(blog|services|pricing|contact|about|website-|ecommerce|seo-|industries)#i', $html);

        $dupTitle = Post::where('title', $post->title)->where('id', '!=', $post->id ?? 0)->exists();
        $metaOk = filled($post->seo?->description) || filled($post->excerpt);

        $issues = [
            ['label' => 'Has an introduction', 'level' => 'warn', 'ok' => $introWords >= 20],
            ['label' => 'Has H2 subheadings', 'level' => 'warn', 'ok' => (bool) preg_match('/<h2\b/i', $html)],
            ['label' => 'Has a closing/conclusion paragraph', 'level' => 'info', 'ok' => $paraCount >= 2],
            ['label' => 'Content is substantial (≥ 300 words)', 'level' => 'warn', 'ok' => $words >= 300],
            ['label' => 'No overly long paragraphs (≤ 150 words)', 'level' => 'info', 'ok' => $longestPara <= 150],
            ['label' => 'Has a featured image', 'level' => 'warn', 'ok' => filled($post->featured_image)],
            ['label' => 'Featured image has ALT text', 'level' => 'warn', 'ok' => filled($post->image_alt)],
            ['label' => 'Has a meta description', 'level' => 'warn', 'ok' => $metaOk],
            ['label' => 'Links to other pages (internal links)', 'level' => 'info', 'ok' => $hasInternalLink],
            ['label' => 'Has at least one FAQ', 'level' => 'info', 'ok' => $post->exists ? $post->faqs()->count() > 0 : false],
            ['label' => 'No broken internal links', 'level' => 'warn', 'ok' => count($broken) === 0],
            ['label' => 'Title is unique', 'level' => 'warn', 'ok' => ! $dupTitle],
        ];

        $passed = collect($issues)->where('ok', true)->count();
        $score = (int) round($passed / count($issues) * 100);

        return ['score' => $score, 'issues' => $issues, 'broken' => $broken];
    }

    /** Return internal /blog/{slug} links in the HTML whose target post no longer exists. */
    public static function brokenInternalLinks(string $html): array
    {
        if (! preg_match_all('#href=["\']/blog/([a-z0-9\-]+)["\']#i', $html, $m)) {
            return [];
        }

        $slugs = array_values(array_unique($m[1]));
        $existing = Post::whereIn('slug', $slugs)->pluck('slug')->all();

        return array_values(array_diff($slugs, $existing));
    }
}
