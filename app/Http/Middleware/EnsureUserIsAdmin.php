<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || ! $user->canAccessAdmin()) {
            Auth::logout();

            return redirect()->route('admin.login')
                ->withErrors(['email' => 'You do not have access to the admin panel.']);
        }

        return $next($request);
    }
}
