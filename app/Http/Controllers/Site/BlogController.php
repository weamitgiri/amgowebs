<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;
use App\Services\SeoService;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(SeoService $seo): View
    {
        $page = Page::where('slug', 'blog')->first();

        return view('site.blog.index', [
            'posts' => Post::published()->with('category')->latest('published_at')->paginate(9),
            'categories' => Category::has('posts')->get(),
            'heading' => 'Blog &amp; Insights',
            'seo' => $seo->resolve($page),
        ]);
    }

    public function category(Category $category, SeoService $seo): View
    {
        return view('site.blog.index', [
            'posts' => $category->posts()->published()->with('category')->latest('published_at')->paginate(9),
            'categories' => Category::has('posts')->get(),
            'heading' => $category->name,
            'seo' => $seo->resolve($category, ['title' => $category->name.' Articles']),
        ]);
    }

    public function tag(Tag $tag, SeoService $seo): View
    {
        return view('site.blog.index', [
            'posts' => $tag->posts()->published()->with('category')->latest('published_at')->paginate(9),
            'categories' => Category::has('posts')->get(),
            'heading' => 'Tag: '.$tag->name,
            'seo' => $seo->resolve(null, ['title' => $tag->name.' Articles']),
        ]);
    }

    public function show(string $slug, SeoService $seo): View
    {
        $post = Post::published()->with(['category', 'tags', 'author', 'visibleFaqs'])->where('slug', $slug)->firstOrFail();

        $brand = Setting::get('seo_organization_name', Setting::get('site_name', config('app.name')));
        $image = $post->social_image ?: $post->featured_image;

        $schema = array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => \Illuminate\Support\Str::limit($post->title, 110, ''),
            'description' => $post->excerpt,
            'image' => $image ? url($image) : null,
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'author' => ['@type' => 'Person', 'name' => $post->author?->name ?? $brand],
            'publisher' => array_filter([
                '@type' => 'Organization',
                'name' => $brand,
                'logo' => Setting::get('logo') ? ['@type' => 'ImageObject', 'url' => url(Setting::get('logo'))] : null,
            ]),
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => route('blog.show', $post->slug)],
            'articleSection' => $post->category?->name,
            'keywords' => $post->tags->pluck('name')->implode(', ') ?: null,
            'wordCount' => str_word_count(strip_tags((string) $post->body)),
        ], fn ($v) => !is_null($v) && $v !== '');

        $overrides = ['schema' => $schema, 'og_type' => 'article'];
        if ($image) {
            $overrides['og_image'] = url($image);
        }

        return view('site.blog.show', [
            'post' => $post,
            'related' => Post::published()->where('id', '!=', $post->id)
                ->where('category_id', $post->category_id)->latest('published_at')->limit(3)->get(),
            'seo' => $seo->resolve($post, $overrides),
        ]);
    }
}
