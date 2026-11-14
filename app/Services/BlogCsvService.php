<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Support\HtmlSanitizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Blog CSV import/export engine.
 *
 * - UTF-8 (Hindi + English), multiline HTML content, commas/quotes handled by fgetcsv.
 * - Every row is validated; import runs in a DB transaction.
 * - Updates match by `id` then `slug`; blank cells never overwrite existing values.
 * - Content is HTML-sanitised; no scripts are imported.
 * - Multiple tags: comma-separated. Multiple FAQs: " | " separated, paired by index.
 */
class BlogCsvService
{
    /** @var list<string> Canonical column order. */
    public const COLUMNS = [
        'id', 'title', 'slug', 'excerpt', 'short_description', 'content', 'category', 'subcategory',
        'tags', 'author', 'status', 'featured_image', 'featured_image_alt', 'image_caption',
        'focus_keyword', 'secondary_keywords', 'search_intent', 'meta_title', 'meta_description',
        'canonical_url', 'robots_index', 'robots_follow', 'og_title', 'og_description', 'og_image',
        'twitter_card', 'twitter_title', 'twitter_description', 'twitter_image',
        'article_schema_enabled', 'faq_schema_enabled', 'published_at', 'scheduled_at',
        'is_featured', 'reading_time', 'faq_questions', 'faq_answers', 'created_at', 'updated_at',
    ];

    public function streamTemplate(): StreamedResponse
    {
        $samples = [
            [
                'title' => 'How to Pick a Business Name', 'slug' => 'how-to-pick-a-business-name',
                'excerpt' => 'A short, practical guide to naming your business.',
                'content' => '<h2>Start with clarity</h2><p>Keep it short and memorable.</p><h2>Check availability</h2><p>Verify the domain and socials.</p>',
                'category' => 'Business Growth', 'tags' => 'branding, small-business', 'author' => '', 'status' => 'published',
                'featured_image' => '', 'featured_image_alt' => 'Business naming guide',
                'focus_keyword' => 'business name', 'meta_title' => 'How to Pick a Business Name',
                'meta_description' => 'A short, practical guide to choosing a memorable, available business name.',
                'robots_index' => '1', 'robots_follow' => '1', 'published_at' => now()->toDateString(),
                'faq_questions' => 'Does the name matter? | Should I buy the .com?',
                'faq_answers' => 'Yes, a lot. | If you can, yes — it is the most trusted.',
            ],
            [
                'title' => 'वेबसाइट क्यों ज़रूरी है', 'slug' => 'website-kyon-zaroori-hai',
                'excerpt' => 'छोटे व्यवसाय के लिए वेबसाइट के फायदे।',
                'content' => '<h2>भरोसा बनता है</h2><p>एक पेशेवर वेबसाइट ग्राहकों का भरोसा बढ़ाती है।</p>',
                'category' => 'Business Growth', 'tags' => 'small-business', 'status' => 'draft',
                'focus_keyword' => 'website', 'meta_description' => 'जानें छोटे व्यवसाय के लिए वेबसाइट क्यों ज़रूरी है।',
                'robots_index' => '1', 'robots_follow' => '1',
            ],
        ];

        return $this->stream('blog-template.csv', function ($out) use ($samples) {
            fputcsv($out, self::COLUMNS);
            foreach ($samples as $s) {
                fputcsv($out, array_map(fn ($c) => $s[$c] ?? '', self::COLUMNS));
            }
        });
    }

    public function streamExport(Builder $query, string $name = 'blog-export'): StreamedResponse
    {
        return $this->stream($name.'-'.now()->format('Y-m-d').'.csv', function ($out) use ($query) {
            fputcsv($out, self::COLUMNS);
            $query->with(['category.parent', 'author', 'tags', 'seo', 'faqs'])->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $p) {
                    $seo = $p->seo;
                    $robots = (string) ($seo?->robots ?? 'index, follow');
                    fputcsv($out, [
                        $p->id, $p->title, $p->slug, $p->excerpt, '', $p->body,
                        $p->category?->parent?->name ? $p->category->parent->name : $p->category?->name,
                        $p->category?->parent ? $p->category->name : '',
                        $p->tags->pluck('name')->implode(', '), $p->author?->name, $p->state,
                        $p->featured_image, $p->image_alt, '',
                        $seo?->focus_keyword, $seo?->secondary_keywords, $seo?->search_intent,
                        $seo?->title, $seo?->description, $seo?->canonical_url,
                        str_contains($robots, 'noindex') ? '0' : '1', str_contains($robots, 'nofollow') ? '0' : '1',
                        $seo?->og_title, $seo?->og_description, $seo?->og_image,
                        '', '', '', '',
                        '', $p->faqs->isNotEmpty() ? '1' : '0',
                        optional($p->published_at)->toDateTimeString(), '',
                        '', $p->reading_time,
                        $p->faqs->pluck('question')->implode(' | '), $p->faqs->pluck('answer')->implode(' | '),
                        optional($p->created_at)->toDateTimeString(), optional($p->updated_at)->toDateTimeString(),
                    ]);
                }
            });
        });
    }

    /** Parse an uploaded CSV file into header-keyed rows. */
    public function parse(string $path): array
    {
        $rows = [];
        if (! ($handle = fopen($path, 'r'))) {
            return $rows;
        }

        $header = null;
        while (($data = fgetcsv($handle)) !== false) {
            if ($header === null) {
                // Strip a UTF-8 BOM from the first cell, normalise header keys.
                $data[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string) $data[0]);
                $header = array_map(fn ($h) => Str::slug(trim((string) $h), '_'), $data);

                continue;
            }
            if (count(array_filter($data, fn ($v) => trim((string) $v) !== '')) === 0) {
                continue; // skip blank lines
            }
            $row = [];
            foreach ($header as $i => $key) {
                $row[$key] = $data[$i] ?? '';
            }
            $rows[] = $row;
        }
        fclose($handle);

        return $rows;
    }

    /** Validate each row and attach the matching post + intended action. */
    public function analyze(array $rows): array
    {
        $out = [];
        $seenSlugs = [];

        foreach ($rows as $n => $row) {
            $errors = [];
            $title = trim((string) ($row['title'] ?? ''));
            $id = (int) ($row['id'] ?? 0);
            $slug = Str::slug((string) ($row['slug'] ?? '')) ?: Str::slug($title);

            $existing = $id > 0 ? Post::find($id) : ($slug ? Post::where('slug', $slug)->first() : null);

            if ($title === '' && ! $existing) {
                $errors[] = 'Title is required for new blogs.';
            }
            if ($slug === '' && $title === '' && ! $existing) {
                $errors[] = 'A title or slug is required.';
            }
            $status = strtolower(trim((string) ($row['status'] ?? 'draft')));
            if ($status !== '' && ! in_array($status, ['draft', 'published', 'scheduled'], true)) {
                $errors[] = "Unknown status “{$status}”.";
            }
            if ($slug && isset($seenSlugs[$slug])) {
                $errors[] = "Duplicate slug “{$slug}” within this file.";
            }
            $seenSlugs[$slug] = true;

            $out[] = [
                'line' => $n + 2, // + header + 1-based
                'row' => $row,
                'title' => $title ?: $existing?->title,
                'slug' => $slug,
                'errors' => $errors,
                'existing' => $existing,
                'action' => ! empty($errors) ? 'error' : ($existing ? 'update' : 'create'),
            ];
        }

        return $out;
    }

    /**
     * Import analysed rows. Returns a summary report.
     *
     * @return array{created:int, updated:int, skipped:int, failed:int, messages:list<string>}
     */
    public function import(array $analysis, bool $updateExisting, bool $skipDuplicates, int $authorId): array
    {
        $summary = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'failed' => 0, 'messages' => []];

        foreach ($analysis as $item) {
            if ($item['action'] === 'error') {
                $summary['failed']++;
                $summary['messages'][] = "Line {$item['line']}: ".implode(' ', $item['errors']);

                continue;
            }
            if ($item['action'] === 'update' && $skipDuplicates) {
                $summary['skipped']++;

                continue;
            }
            if ($item['action'] === 'update' && ! $updateExisting) {
                $summary['skipped']++;

                continue;
            }

            try {
                DB::transaction(fn () => $this->saveRow($item, $authorId));
                $item['existing'] ? $summary['updated']++ : $summary['created']++;
            } catch (\Throwable $e) {
                $summary['failed']++;
                $summary['messages'][] = "Line {$item['line']}: ".$e->getMessage();
            }
        }

        return $summary;
    }

    private function saveRow(array $item, int $authorId): void
    {
        $row = $item['row'];
        $existing = $item['existing'];
        $isUpdate = (bool) $existing;

        // Only set a value when the cell is non-empty (never wipe existing data on update).
        $val = fn (string $key) => trim((string) ($row[$key] ?? ''));
        $set = function (array &$data, string $field, string $key) use ($row, $isUpdate) {
            $v = trim((string) ($row[$key] ?? ''));
            if ($v !== '' || ! $isUpdate) {
                $data[$field] = $v;
            }
        };

        $post = $existing ?: new Post;

        $data = [];
        $set($data, 'title', 'title');
        $set($data, 'excerpt', 'excerpt');
        if ($val('excerpt') === '' && $val('short_description') !== '') {
            $data['excerpt'] = $val('short_description');
        }
        if ($val('content') !== '' || ! $isUpdate) {
            $data['body'] = HtmlSanitizer::clean($val('content'));
        }
        $set($data, 'image_alt', 'featured_image_alt');
        if ($val('featured_image') !== '') {
            $data['featured_image'] = $val('featured_image');
        }

        // Slug
        $data['slug'] = $item['slug'] ?: ($post->slug ?: Str::slug($data['title'] ?? $post->title));

        // Status / dates
        $status = strtolower($val('status')) ?: ($post->status ?: 'draft');
        $publishedAt = $val('published_at') ?: $val('scheduled_at');
        if ($status === 'scheduled') {
            $status = 'published';
            $publishedAt = $publishedAt ?: now()->addDay()->toDateTimeString();
        }
        $data['status'] = $status;
        if ($publishedAt !== '') {
            $data['published_at'] = Carbon::parse($publishedAt);
        } elseif (! $isUpdate && $status === 'published') {
            $data['published_at'] = now();
        }

        // Author
        $author = $val('author');
        $data['author_id'] = $post->author_id ?: $authorId;
        if ($author !== '') {
            $data['author_id'] = User::where('name', $author)->value('id') ?: $data['author_id'];
        }

        // Category / subcategory
        $categoryId = $post->category_id;
        if ($val('category') !== '') {
            $parent = Category::firstOrCreate(['slug' => Str::slug($val('category'))], ['name' => $val('category')]);
            $categoryId = $parent->id;
            if ($val('subcategory') !== '') {
                $child = Category::firstOrCreate(
                    ['slug' => Str::slug($val('subcategory'))],
                    ['name' => $val('subcategory'), 'parent_id' => $parent->id],
                );
                $categoryId = $child->id;
            }
        }
        $data['category_id'] = $categoryId;

        $post->fill($data)->save();

        // Tags
        if ($val('tags') !== '' || ! $isUpdate) {
            $tagIds = collect(explode(',', $val('tags')))->map(fn ($t) => trim($t))->filter()->unique()
                ->map(fn ($name) => Tag::firstOrCreate(['slug' => Str::slug($name)], ['name' => $name])->id);
            $post->tags()->sync($tagIds);
        }

        // SEO
        $seoData = array_filter([
            'title' => $val('meta_title'),
            'description' => $val('meta_description'),
            'focus_keyword' => $val('focus_keyword'),
            'secondary_keywords' => $val('secondary_keywords'),
            'search_intent' => $val('search_intent'),
            'canonical_url' => $val('canonical_url'),
            'og_title' => $val('og_title'),
            'og_description' => $val('og_description'),
            'og_image' => $val('og_image'),
        ], fn ($v) => $v !== '');
        $robotsIndex = $val('robots_index');
        $robotsFollow = $val('robots_follow');
        if ($robotsIndex !== '' || $robotsFollow !== '') {
            $seoData['robots'] = ($robotsIndex === '0' ? 'noindex' : 'index').', '.($robotsFollow === '0' ? 'nofollow' : 'follow');
        }
        if (! empty($seoData)) {
            $post->seo()->updateOrCreate([], $seoData);
        }

        // FAQs — only when provided (don't wipe on a blank update).
        $questions = array_values(array_filter(array_map('trim', explode('|', $val('faq_questions')))));
        $answers = array_values(array_filter(array_map('trim', explode('|', $val('faq_answers')))));
        if ($questions) {
            $post->faqs()->delete();
            foreach ($questions as $i => $q) {
                $post->faqs()->create([
                    'question' => Str::limit($q, 255, ''),
                    'answer' => $answers[$i] ?? '',
                    'sort_order' => $i,
                    'is_active' => true,
                ]);
            }
        }
    }

    private function stream(string $filename, callable $writer): StreamedResponse
    {
        return response()->streamDownload(function () use ($writer) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 BOM for Excel
            $writer($out);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
