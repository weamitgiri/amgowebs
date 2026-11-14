<?php

namespace Tests\Feature;

use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCms();
    }

    public function test_admin_can_build_a_page_that_renders_sections(): void
    {
        $admin = $this->adminUser();

        // Create page
        $this->actingAs($admin)->post(route('admin.pages.store'), [
            'title' => 'Our Process',
            'slug' => 'our-process',
            'status' => 'published',
            'is_in_sitemap' => '1',
        ])->assertRedirect();

        $page = Page::where('slug', 'our-process')->first();
        $this->assertNotNull($page);

        // Add a hero section
        $this->actingAs($admin)->post(route('admin.pages.sections.add', $page), ['type' => 'hero'])->assertRedirect();
        $section = $page->sections()->first();
        $this->assertSame('hero', $section->type);

        // Edit the section
        $this->actingAs($admin)->put(route('admin.pages.sections.update', [$page, $section]), [
            'data' => ['heading' => 'How We Build Websites', 'subheading' => 'A simple process.'],
            'is_visible' => '1',
        ])->assertRedirect();

        // Public render
        $this->get('/our-process')->assertOk()->assertSee('How We Build Websites');
    }

    public function test_section_text_fields_are_stripped_of_scripts(): void
    {
        $admin = $this->adminUser();
        $this->actingAs($admin)->post(route('admin.pages.store'), ['title' => 'XSS Test', 'slug' => 'xss-test', 'status' => 'published']);
        $page = Page::where('slug', 'xss-test')->first();
        $this->actingAs($admin)->post(route('admin.pages.sections.add', $page), ['type' => 'hero']);
        $section = $page->sections()->first();

        $this->actingAs($admin)->put(route('admin.pages.sections.update', [$page, $section]), [
            'data' => ['heading' => 'Hello <script>alert(1)</script>'],
            'is_visible' => '1',
        ]);

        $this->assertStringNotContainsString('<script>', $section->fresh()->data['heading']);
    }

    public function test_non_super_admin_cannot_add_custom_html(): void
    {
        $contentManager = $this->userWithRole('content-manager');
        $this->actingAs($contentManager)->post(route('admin.pages.store'), ['title' => 'P', 'slug' => 'p', 'status' => 'draft']);
        $page = Page::where('slug', 'p')->first();

        $this->actingAs($contentManager)
            ->post(route('admin.pages.sections.add', $page), ['type' => 'custom_html'])
            ->assertForbidden();
    }
}
