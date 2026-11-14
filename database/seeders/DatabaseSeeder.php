<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminSeeder::class,
            SettingsSeeder::class,
            MenuSeeder::class,
            PackageSeeder::class,
            PageSeeder::class,
            BlogSeeder::class,
            JobSeeder::class,
            RedirectSeeder::class,
        ]);
    }
}
