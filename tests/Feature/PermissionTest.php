<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCms();
    }

    public function test_sales_manager_can_view_inquiries(): void
    {
        $user = $this->userWithRole('sales-manager');
        $this->actingAs($user)->get(route('admin.inquiries.index'))->assertOk();
    }

    public function test_sales_manager_cannot_access_packages(): void
    {
        $user = $this->userWithRole('sales-manager');
        $this->actingAs($user)->get(route('admin.packages.index'))->assertForbidden();
    }

    public function test_content_manager_can_access_blog_but_not_inquiries(): void
    {
        $user = $this->userWithRole('content-manager');
        $this->actingAs($user)->get(route('admin.posts.index'))->assertOk();
        $this->actingAs($user)->get(route('admin.inquiries.index'))->assertForbidden();
    }

    public function test_super_admin_can_access_everything(): void
    {
        $admin = $this->adminUser();
        foreach (['admin.packages.index', 'admin.inquiries.index', 'admin.posts.index', 'admin.settings.edit', 'admin.users.index'] as $route) {
            $this->actingAs($admin)->get(route($route))->assertOk();
        }
    }
}
