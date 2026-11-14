<?php

namespace App\Providers;

use App\View\Composers\SiteComposer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Super admins bypass every gate check.
        Gate::before(fn ($user) => $user->isSuperAdmin() ? true : null);

        // @permission('packages.edit') ... @elsepermission ... @endpermission
        Blade::if('permission', function (string $permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });

        // Share site settings + navigation menus with the public layout.
        View::composer(['layouts.site', 'partials.site.*'], SiteComposer::class);
    }
}
