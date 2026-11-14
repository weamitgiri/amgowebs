<?php

namespace Tests\Feature;

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCms();
    }

    public function test_admin_can_create_a_package_with_features(): void
    {
        $this->actingAs($this->adminUser())->post(route('admin.packages.store'), [
            'name' => 'Launch Pack',
            'price_usd' => 149,
            'short_description' => 'A quick-launch website.',
            'status' => 'published',
            'is_active' => '1',
            'cta_label' => 'Get Started',
            'included' => ['3 pages', 'Contact form'],
            'excluded' => ['Hosting'],
            'addon_label' => ['Extra page'],
            'addon_price' => ['20'],
        ])->assertRedirect(route('admin.packages.index'));

        $package = Package::where('slug', 'launch-pack')->first();
        $this->assertNotNull($package);
        $this->assertSame('149.00', $package->price_usd);
        $this->assertSame(2, $package->includedFeatures()->count());
        $this->assertSame(1, $package->excludedFeatures()->count());
        $this->assertSame(1, $package->addonFeatures()->count());
    }

    public function test_custom_pricing_clears_price(): void
    {
        $this->actingAs($this->adminUser())->post(route('admin.packages.store'), [
            'name' => 'Enterprise',
            'is_custom_pricing' => '1',
            'price_usd' => 999,
            'status' => 'published',
            'cta_label' => 'Request a Quote',
        ])->assertRedirect();

        $package = Package::where('slug', 'enterprise')->first();
        $this->assertNull($package->price_usd);
        $this->assertSame('Custom', $package->price_label);
    }

    public function test_public_pricing_page_lists_published_packages(): void
    {
        $this->seed(\Database\Seeders\PackageSeeder::class);

        $this->get(route('pricing'))
            ->assertOk()
            ->assertSee('Starter Website')
            ->assertSee('$199');
    }

    public function test_draft_packages_are_hidden_from_public(): void
    {
        Package::create(['name' => 'Secret', 'slug' => 'secret', 'price_usd' => 50, 'status' => 'draft', 'is_active' => true]);

        $this->get(route('pricing'))->assertOk()->assertDontSee('Secret');
    }
}
