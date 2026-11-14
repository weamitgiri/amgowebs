<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seedCms();
    }

    public function test_guests_are_redirected_from_admin(): void
    {
        $this->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_admin_can_log_in(): void
    {
        $admin = $this->adminUser();

        $this->post(route('admin.login.attempt'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $admin = $this->adminUser();

        $this->from(route('admin.login'))
            ->post(route('admin.login.attempt'), ['email' => $admin->email, 'password' => 'wrong'])
            ->assertRedirect(route('admin.login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_user_cannot_access_admin(): void
    {
        $user = $this->userWithRole('content-manager', ['is_active' => false]);

        $this->actingAs($user)->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_user_without_role_cannot_access_admin(): void
    {
        $user = \App\Models\User::factory()->create(['role_id' => null, 'is_active' => true]);

        $this->actingAs($user)->get('/admin')->assertRedirect(route('admin.login'));
    }

    public function test_dashboard_loads_for_admin(): void
    {
        $this->actingAs($this->adminUser())->get('/admin')->assertOk()->assertSee('Dashboard');
    }
}
