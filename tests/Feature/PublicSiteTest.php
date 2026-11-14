<?php

namespace Tests\Feature;

use Database\Seeders\PackageSeeder;
use Database\Seeders\PageSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicSiteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCms();
        $this->seed([PackageSeeder::class, PageSeeder::class]);
    }

    public function test_public_pages_load(): void
    {
        $this->get('/')->assertOk()->assertSee('Professional Websites');
        $this->get(route('pricing'))->assertOk();
        $this->get(route('blog.index'))->assertOk();
        $this->get(route('contact'))->assertOk();
    }

    public function test_package_detail_page_loads(): void
    {
        $this->get(route('packages.show', 'business-website'))->assertOk()->assertSee('Business Website');
    }

    public function test_cms_page_renders_via_fallback(): void
    {
        $this->get('/about')->assertOk()->assertSee('About');
    }

    public function test_unknown_page_returns_404(): void
    {
        $this->get('/this-page-does-not-exist')->assertNotFound();
    }

    public function test_sitemap_is_generated(): void
    {
        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('<loc>', false)
            ->assertSee('business-website', false);
    }

    public function test_robots_disallows_admin(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Disallow: /admin')
            ->assertSee('Sitemap:');
    }

    public function test_security_headers_present(): void
    {
        $this->get('/')
            ->assertHeader('X-Frame-Options', 'SAMEORIGIN')
            ->assertHeader('X-Content-Type-Options', 'nosniff');
    }
}
