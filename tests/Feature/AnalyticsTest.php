<?php

namespace Tests\Feature;

use App\Models\Visit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCms();
    }

    public function test_public_page_view_is_tracked(): void
    {
        $this->withHeaders(['User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15)'])
            ->get('/')->assertOk();

        $this->assertGreaterThan(0, Visit::count());
    }

    public function test_bots_and_admin_are_not_tracked(): void
    {
        $this->withHeaders(['User-Agent' => 'Googlebot/2.1'])->get('/');
        $admin = $this->adminUser();
        $this->actingAs($admin)->get(route('admin.dashboard'));

        $this->assertSame(0, Visit::count());
    }

    public function test_visitors_dashboard_renders(): void
    {
        $admin = $this->adminUser();
        Visit::create(['country' => 'US', 'country_name' => 'United States', 'path' => '/', 'device' => 'desktop', 'visitor_hash' => 'h1', 'created_at' => now()]);
        Visit::create(['country' => 'IN', 'country_name' => 'India', 'path' => '/blog', 'device' => 'mobile', 'visitor_hash' => 'h2', 'created_at' => now()]);

        $this->actingAs($admin)->get(route('admin.analytics.index'))->assertOk()->assertSee('United States');
        $this->actingAs($admin)->get(route('admin.analytics.index', ['days' => 7]))->assertOk();
    }
}
