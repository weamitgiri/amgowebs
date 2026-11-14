<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use App\Services\ImageService;
use App\Services\SeoService;
use App\Support\HtmlSanitizer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse as BaseStreamedResponse;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        $now = now();

        return view('admin.posts.index', [
            'posts' => $this->filtered($request)->paginate(15)->withQueryString(),
            'stats' => [
                'total' => Post::count(),
                'published' => Post::where('status', 'published')->where('published_at', '<=', $now)->count(),
                'draft' => Post::where('status', 'draft')->count(),
                'scheduled' => Post::where('status', 'published')->where('published_at', '>', $now)->count(),
                'categories' => Category::count(),
                'views' => (int) Post::sum('views'),
            ],
            'categories' => Category::orderBy('name')->get(),
            'authors' => User::whereNotNull('role_id')->orderBy('name')->get(),
            'filters' => $request->only('q', 'category', 'author', 'status', 'from', 'to'),
        ]);
    }

    /** Build the filtered listing query shared by the table and CSV export. */
    private function filtered(Request $request): Builder
    {
        $query = Post::with(['category', 'author', 'seo']);

        if ($q = trim((string) $request->query('q'))) {
            $query->where(fn ($x) => $x->where('title', 'like', "%{$q}%")
                ->orWhere('slug', 'like', "%{$q}%")
                ->orWhere('excerpt', 'like', "%{$q}%"));
        }
        if ($cat = $request->query('category')) {
            $query->where('category_id', $cat);
        }
        if ($author = $request->query('author')) {
            $query->where('author_id', $author);
        }
        if ($status = $request->query('status')) {
            match ($status) {
                'draft' => $query->where('status', 'draft'),
                'scheduled' => $query->where('status', 'published')->where('published_at', '>', now()),
                'published' => $query->where('status', 'published')->where('published_at', '<=', now()),
                default => null,
            };
        }
        if ($from = $request->query('from')) {
            $query->whereDate('published_at', '>=', $from);
        }
        if ($to = $request->query('to')) {
            $query->whereDate('published_at', '<=', $to);
        }

        return $query->latest('id');
    }

    public function create(): View
    {
        return view('admin.posts.form', [
            'post' => new Post(['status' => 'draft']),
            'categories' => Category::with('parent')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);
        $post = Post::create($this->payload($request, $data));
        $this->syncRelations($post, $request);
        ActivityLog::record('post.created', "Post “{$post->title}” created", $post);

        return redirect()->route('admin.posts.index')->with('status', 'Post created.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.form', [
            'post' => $post->load('tags', 'faqs', 'seo'),
            'categories' => Category::with('parent')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Post $post): RedirectResponse
    {
        $data = $this->validateData($request, $post);
        $post->update($this->payload($request, $data));
        $this->syncRelations($post, $request);
        ActivityLog::record('post.updated', "Post “{$post->title}” updated", $post);

        return redirect()->route('admin.posts.index')->with('status', 'Post updated.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $title = $post->title;
        $post->delete();
        ActivityLog::record('post.deleted', "Post “{$title}” deleted");

        return redirect()->route('admin.posts.index')->with('status', 'Post deleted.');
    }

    /** Duplicate a post as a fresh draft (content, image, SEO and tags copied). */
    public function duplicate(Post $post): RedirectResponse
    {
        $copy = $post->replicate(['views']);
        $copy->title = Str::limit($post->title.' (Copy)', 200, '');
        $copy->slug = Str::limit($post->slug, 210, '').'-copy-'.Str::lower(Str::random(4));
        $copy->status = 'draft';
        $copy->published_at = null;
        $copy->views = 0;
        $copy->save();

        $copy->tags()->sync($post->tags->pluck('id'));
        if ($post->seo) {
            $copy->seo()->create($post->seo->only([
                'title', 'description', 'canonical_url', 'robots', 'og_title',
                'og_description', 'og_image', 'focus_keyword',
            ]));
        }
        ActivityLog::record('post.duplicated', "Post “{$post->title}” duplicated", $copy);

        return redirect()->route('admin.posts.edit', $copy)->with('status', 'Post duplicated as a draft.');
    }

    /** Authenticated preview using the real frontend template (drafts never public). */
    public function preview(Post $post, SeoService $seo): View
    {
        $post->load(['category', 'tags', 'author', 'seo']);

        return view('site.blog.show', [
            'post' => $post,
            'related' => collect(),
            'seo' => $seo->resolve($post, ['robots' => 'noindex, nofollow', 'og_type' => 'article']),
        ]);
    }

    /** Summernote in-editor image upload — stores an optimised WebP and returns its URL. */
    public function imageUpload(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate(['file' => ['required', 'file', 'mimes:jpeg,jpg,png,gif,webp', 'max:8192']]);
        $url = app(ImageService::class)->storeInline($request->file('file'));
        abort_if(! $url, 422, 'Could not process the image.');

        return response()->json(['url' => $url]);
    }

    public function bulk(Request $request): RedirectResponse|BaseStreamedResponse
    {
        $data = $request->validate([
            'bulk_action' => ['required', 'in:publish,draft,delete,category,export'],
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
            'bulk_category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $ids = $data['ids'];

        if ($data['bulk_action'] === 'export') {
            return $this->exportCsv(Post::with(['category', 'author', 'seo'])->whereIn('id', $ids)->latest('id'));
        }

        $count = count($ids);
        switch ($data['bulk_action']) {
            case 'publish':
                Post::whereIn('id', $ids)->whereNull('published_at')->update(['published_at' => now()]);
                Post::whereIn('id', $ids)->update(['status' => 'published']);
                break;
            case 'draft':
                Post::whereIn('id', $ids)->update(['status' => 'draft']);
                break;
            case 'category':
                Post::whereIn('id', $ids)->update(['category_id' => $data['bulk_category_id'] ?? null]);
                break;
            case 'delete':
                Post::whereIn('id', $ids)->delete();
                break;
        }
        ActivityLog::record('post.bulk', "Bulk “{$data['bulk_action']}” on {$count} post(s)");

        return back()->with('status', ucfirst($data['bulk_action'])." applied to {$count} post(s).");
    }

    public function export(Request $request): BaseStreamedResponse
    {
        return $this->exportCsv($this->filtered($request));
    }

    private function exportCsv(Builder $query): BaseStreamedResponse
    {
        $columns = ['ID', 'Title', 'Slug', 'Category', 'Author', 'Status', 'SEO Score', 'Focus Keyword', 'Meta Title', 'Meta Description', 'Views', 'Published At', 'Updated At', 'URL'];

        return response()->streamDownload(function () use ($query, $columns) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $columns);
            $query->chunk(200, function ($rows) use ($out) {
                foreach ($rows as $p) {
                    fputcsv($out, [
                        $p->id, $p->title, $p->slug, $p->category?->name, $p->author?->name,
                        $p->state, $p->seo_score, $p->seo?->focus_keyword, $p->seo?->title, $p->seo?->description,
                        $p->views, optional($p->published_at)->format('Y-m-d H:i'), $p->updated_at->format('Y-m-d H:i'),
                        url('/blog/'.$p->slug),
                    ]);
                }
            });
            fclose($out);
        }, 'blog-posts-'.now()->format('Y-m-d').'.csv', ['Content-Type' => 'text/csv']);
    }

    private function validateData(Request $request, ?Post $post = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220', Rule::unique('posts', 'slug')->ignore($post?->id)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'body' => ['nullable', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'new_category' => ['nullable', 'string', 'max:100'],
            'tags' => ['nullable', 'string', 'max:500'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
            'featured_image' => ['nullable', 'mimes:jpeg,jpg,png,gif,webp', 'max:8192'],
            'image_alt' => ['nullable', 'string', 'max:160'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'focus_keyword' => ['nullable', 'string', 'max:120'],
            'secondary_keywords' => ['nullable', 'string', 'max:255'],
            'search_intent' => ['nullable', 'string', 'max:40'],
            'canonical_url' => ['nullable', 'url', 'max:255'],
            'robots_index' => ['nullable', 'boolean'],
            'robots_follow' => ['nullable', 'boolean'],
            'og_title' => ['nullable', 'string', 'max:255'],
            'og_description' => ['nullable', 'string', 'max:500'],
            'og_image' => ['nullable', 'url', 'max:255'],
            'faqs' => ['nullable', 'array'],
            'faqs.*.question' => ['nullable', 'string', 'max:255'],
            'faqs.*.answer' => ['nullable', 'string', 'max:2000'],
            'faqs.*.is_active' => ['nullable', 'boolean'],
        ]);
    }

    private function payload(Request $request, array $data): array
    {
        $categoryId = $data['category_id'] ?? null;
        if (! empty($data['new_category'])) {
            $categoryId = Category::firstOrCreate(
                ['slug' => Str::slug($data['new_category'])],
                ['name' => $data['new_category']],
            )->id;
        }

        $publishedAt = $data['published_at'] ?? null;
        if ($data['status'] === 'published' && ! $publishedAt) {
            $publishedAt = now();
        }

        $slug = ($data['slug'] ?? null) ?: Str::slug($data['title']);

        $payload = [
            'title' => $data['title'],
            'slug' => $slug,
            'excerpt' => $data['excerpt'] ?? null,
            'body' => HtmlSanitizer::clean($data['body'] ?? null),
            'category_id' => $categoryId,
            'status' => $data['status'],
            'published_at' => $publishedAt,
            'author_id' => auth()->id(),
            'image_alt' => $data['image_alt'] ?? null,
        ];

        if ($request->hasFile('featured_image')) {
            $variants = app(ImageService::class)->processBlogImage($request->file('featured_image'), $slug);
            if ($variants) {
                $payload['featured_image'] = $variants['banner'];
                $payload['thumbnail'] = $variants['thumbnail'];
            }
        }

        return $payload;
    }

    private function syncRelations(Post $post, Request $request): void
    {
        $tagNames = collect(explode(',', (string) $request->input('tags')))
            ->map(fn ($t) => trim($t))->filter()->unique();
        $tagIds = $tagNames->map(fn ($name) => Tag::firstOrCreate(
            ['slug' => Str::slug($name)], ['name' => $name]
        )->id);
        $post->tags()->sync($tagIds);

        // SEO metadata (full wizard). Robots is assembled from the two checkboxes.
        $robots = ($request->boolean('robots_index') ? 'index' : 'noindex')
            .', '.($request->boolean('robots_follow') ? 'follow' : 'nofollow');

        $post->seo()->updateOrCreate([], [
            'title' => $request->input('seo_title'),
            'description' => $request->input('seo_description'),
            'focus_keyword' => $request->input('focus_keyword'),
            'secondary_keywords' => $request->input('secondary_keywords'),
            'search_intent' => $request->input('search_intent'),
            'canonical_url' => $request->input('canonical_url'),
            'robots' => $robots,
            'og_title' => $request->input('og_title'),
            'og_description' => $request->input('og_description'),
            'og_image' => $request->input('og_image'),
        ]);

        // FAQs — replace with the submitted set, keeping order (rows with no Q&A are skipped).
        $post->faqs()->delete();
        foreach (array_values((array) $request->input('faqs', [])) as $i => $faq) {
            if (blank($faq['question'] ?? null) && blank($faq['answer'] ?? null)) {
                continue;
            }
            $post->faqs()->create([
                'question' => Str::limit((string) ($faq['question'] ?? ''), 255, ''),
                'answer' => (string) ($faq['answer'] ?? ''),
                'sort_order' => $i,
                'is_active' => ! empty($faq['is_active']),
            ]);
        }
    }
}
