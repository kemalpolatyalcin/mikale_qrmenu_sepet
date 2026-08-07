<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class AdvancedIpRateLimiter
{
    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();
        $banKey = 'ip_ban_' . $ip;

        if (Cache::has($banKey)) {
            abort(403, 'Cok fazla istek attiginiz icin erisiminiz gecici olarak engellendi.');
        }

        $limiterKey = 'ip_req_limit_' . $ip;
        
        RateLimiter::hit($limiterKey, 60);

        if (RateLimiter::attempts($limiterKey) > 20) {
            Cache::put($banKey, true, 3600);
            RateLimiter::clear($limiterKey);
            abort(403, 'Cok fazla istek attiginiz icin erisiminiz gecici olarak engellendi.');
        }

        return $next($request);
    }
}
