<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BlogAdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCms();
    }

    public function test_all_blog_admin_pages_render(): void
    {
        $admin = $this->adminUser();
        $cat = Category::create(['name' => 'Tech', 'slug' => 'tech']);
        $post = Post::create([
            'title' => 'Sample Post', 'slug' => 'sample-post', 'body' => '<h2>Heading</h2><p>Body text here.</p>',
            'status' => 'published', 'published_at' => now(), 'author_id' => $admin->id, 'category_id' => $cat->id,
        ]);
        $post->faqs()->create(['question' => 'Q?', 'answer' => 'A.', 'sort_order' => 0, 'is_active' => true]);
        $post->seo()->create(['focus_keyword' => 'sample', 'robots' => 'index, follow']);

        $urls = [
            route('admin.posts.index'),
            route('admin.posts.create'),
            route('admin.posts.edit', $post),
            route('admin.posts.preview', $post),
            route('admin.posts.preview-frame', $post),
            route('admin.posts.link-search', ['q' => 'sample']),
            route('admin.categories.index'),
            route('admin.categories.create'),
            route('admin.categories.edit', $cat),
            route('admin.tags.index'),
            route('admin.blog-csv.index'),
            route('admin.blog-csv.template'),
        ];

        foreach ($urls as $url) {
            $this->actingAs($admin)->get($url)->assertOk();
        }
    }

    public function test_blog_create_saves_seo_and_faqs(): void
    {
        $admin = $this->adminUser();

        $this->actingAs($admin)->post(route('admin.posts.store'), [
            'title' => 'New Blog', 'status' => 'published', 'body' => '<h2>Hi</h2><p>Body.</p>',
            'focus_keyword' => 'new blog', 'seo_description' => 'A meta description for the new blog post.',
            'robots_index' => '1', 'robots_follow' => '1',
            'faqs' => [['question' => 'Q1?', 'answer' => 'A1.', 'is_active' => '1']],
            'tags' => 'alpha, beta',
        ])->assertRedirect(route('admin.posts.index'));

        $post = Post::where('slug', 'new-blog')->firstOrFail();
        $this->assertSame('new blog', $post->seo->focus_keyword);
        $this->assertCount(1, $post->faqs);
        $this->assertCount(2, $post->tags);
    }
}
