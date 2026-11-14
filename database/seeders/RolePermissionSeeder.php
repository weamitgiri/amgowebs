<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class RolePermissionSeeder extends Seeder
{
    /**
     * Permission groups => list of [slug => label].
     */
    private array $permissions = [
        'Dashboard' => ['dashboard.view' => 'View dashboard'],
        'Pages' => [
            'pages.view' => 'View pages', 'pages.create' => 'Create pages',
            'pages.edit' => 'Edit pages', 'pages.delete' => 'Delete pages', 'pages.publish' => 'Publish pages',
        ],
        'Packages' => [
            'packages.view' => 'View packages', 'packages.create' => 'Create packages',
            'packages.edit' => 'Edit packages', 'packages.delete' => 'Delete packages',
        ],
        'Inquiries' => [
            'inquiries.view' => 'View inquiries', 'inquiries.manage' => 'Manage inquiries',
            'inquiries.assign' => 'Assign inquiries', 'inquiries.export' => 'Export inquiries',
        ],
        'Blog' => [
            'blog.view' => 'View blog', 'blog.create' => 'Create posts',
            'blog.edit' => 'Edit posts', 'blog.delete' => 'Delete posts', 'blog.publish' => 'Publish posts',
        ],
        'Careers' => [
            'jobs.view' => 'View job openings', 'jobs.create' => 'Create job openings',
            'jobs.edit' => 'Edit job openings', 'jobs.delete' => 'Delete job openings',
        ],
        'Media' => ['media.view' => 'View media', 'media.manage' => 'Manage media'],
        'Menus' => ['menus.manage' => 'Manage navigation'],
        'SEO' => ['seo.view' => 'View SEO', 'seo.manage' => 'Manage SEO'],
        'Settings' => ['settings.manage' => 'Manage settings'],
        'Users' => ['users.view' => 'View users', 'users.manage' => 'Manage users'],
        'Roles' => ['roles.manage' => 'Manage roles'],
        'Activity' => ['activity.view' => 'View activity log'],
    ];

    public function run(): void
    {
        $all = [];
        foreach ($this->permissions as $group => $perms) {
            foreach ($perms as $slug => $name) {
                $all[] = Permission::updateOrCreate(['slug' => $slug], ['name' => $name, 'group' => Str::slug($group)]);
            }
        }

        $byGroup = fn (array $groups) => Permission::whereIn('group', $groups)->pluck('id')->all();

        $roles = [
            ['super-admin', 'Super Admin', 'Full access to everything', 'ALL'],
            ['administrator', 'Administrator', 'Manage all content and settings', 'ALL'],
            ['content-manager', 'Content Manager', 'Manage pages, blog, careers, media and menus',
                array_merge($byGroup(['dashboard', 'pages', 'blog', 'careers', 'media', 'menus']))],
            ['seo-manager', 'SEO Manager', 'Manage SEO settings and metadata',
                Permission::whereIn('slug', ['dashboard.view', 'seo.view', 'seo.manage', 'pages.view', 'pages.edit', 'blog.view', 'blog.edit'])->pluck('id')->all()],
            ['sales-manager', 'Sales Manager', 'Manage project inquiries and leads',
                Permission::whereIn('slug', ['dashboard.view', 'inquiries.view', 'inquiries.manage', 'inquiries.assign', 'inquiries.export'])->pluck('id')->all()],
        ];

        foreach ($roles as [$slug, $name, $desc, $perms]) {
            $role = Role::updateOrCreate(['slug' => $slug], ['name' => $name, 'description' => $desc, 'is_system' => true]);
            $ids = $perms === 'ALL' ? Permission::pluck('id')->all() : $perms;
            $role->permissions()->sync($ids);
        }
    }
}
