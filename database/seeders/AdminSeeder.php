<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('ADMIN_EMAIL');
        $password = env('ADMIN_PASSWORD');

        if (blank($email) || blank($password)) {
            $this->command->warn('ADMIN_EMAIL / ADMIN_PASSWORD not set in .env — skipping admin creation.');

            return;
        }

        $superAdmin = Role::where('slug', 'super-admin')->first();

        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('ADMIN_NAME', 'Super Admin'),
                'password' => Hash::make($password),
                'role_id' => $superAdmin?->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $this->command->info("Admin account ready: {$user->email}");
    }
}
