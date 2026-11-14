<?php

namespace Database\Seeders;

use App\Models\Redirect;
use Illuminate\Database\Seeder;

class RedirectSeeder extends Seeder
{
    public function run(): void
    {
        // Preserve SEO value / inbound links for retired URLs with 301s.
        $redirects = [
            ['from_path' => 'server-hosting', 'to_path' => '/services', 'status_code' => 301],
            ['from_path' => 'hosting', 'to_path' => '/services', 'status_code' => 301],
        ];

        foreach ($redirects as $r) {
            Redirect::updateOrCreate(
                ['from_path' => $r['from_path']],
                ['to_path' => $r['to_path'], 'status_code' => $r['status_code'], 'is_active' => true],
            );
        }
    }
}
