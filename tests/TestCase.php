<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\MenuSeeder;
use Database\Seeders\RolePermissionSeeder;
use Database\Seeders\SettingsSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Hash;

abstract class TestCase extends BaseTestCase
{
    /** Seed roles/permissions, settings and menus for CMS tests. */
    protected function seedCms(): void
    {
        $this->seed([RolePermissionSeeder::class, SettingsSeeder::class, MenuSeeder::class]);
    }

    protected function adminUser(): User
    {
        return $this->userWithRole('super-admin');
    }

    protected function userWithRole(string $roleSlug, array $attributes = []): User
    {
        $role = Role::where('slug', $roleSlug)->firstOrFail();

        return User::factory()->create(array_merge([
            'role_id' => $role->id,
            'is_active' => true,
            'password' => Hash::make('password'),
        ], $attributes));
    }
}
