<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Services\ImageService;
use App\Support\HtmlSanitizer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        return view('admin.posts.index', [
            'posts' => Post::with('category', 'author')->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        return view('admin.posts.form', [
            'post' => new Post(['status' => 'draft']),
            'categories' => Category::orderBy('name')->get(),
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
            'post' => $post->load('tags'),
            'categories' => Category::orderBy('name')->get(),
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

        // Auto-generate SEO-friendly banner + thumbnail (year/month folders) via GD.
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
        // Tags (comma-separated, created on the fly)
        $tagNames = collect(explode(',', (string) $request->input('tags')))
            ->map(fn ($t) => trim($t))->filter()->unique();
        $tagIds = $tagNames->map(fn ($name) => Tag::firstOrCreate(
            ['slug' => Str::slug($name)], ['name' => $name]
        )->id);
        $post->tags()->sync($tagIds);

        // SEO
        if ($request->filled('seo_title') || $request->filled('seo_description')) {
            $post->seo()->updateOrCreate([], [
                'title' => $request->input('seo_title'),
                'description' => $request->input('seo_description'),
            ]);
        }
    }
}
