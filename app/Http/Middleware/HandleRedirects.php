<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HandleRedirects
{
    public function handle(Request $request, Closure $next): Response
    {
        // Only consider GET requests to non-admin paths.
        if ($request->isMethod('GET') && ! $request->is('admin*')) {
            $path = '/'.ltrim($request->path(), '/');

            $redirect = Redirect::where('is_active', true)
                ->where(fn ($q) => $q->where('from_path', $path)->orWhere('from_path', $request->path()))
                ->first();

            if ($redirect) {
                $redirect->increment('hits');

                return redirect($redirect->to_path, $redirect->status_code);
            }
        }

        return $next($request);
    }
}
