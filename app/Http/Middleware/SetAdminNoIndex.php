<?php

namespace App\Http\Middleware;

// app/Http/Middleware/SetAdminNoIndex.php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;

class SetAdminNoIndex {
    public function handle(Request $request, Closure $next) {
        $response = $next($request);

        $isAdminPath = str_starts_with($request->getPathInfo(), '/admin')
            || str_starts_with(optional($request->route())->getName() ?? '', 'filament.');

        if ($isAdminPath) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }
        return $response;
    }
}

