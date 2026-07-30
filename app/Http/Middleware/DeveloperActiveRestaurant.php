<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DeveloperActiveRestaurant
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            $token = $request->cookie('admin_token');
            if ($token) {
                $user = null;
                if (class_exists(\Laravel\Sanctum\PersonalAccessToken::class)) {
                    $accessToken = \Laravel\Sanctum\PersonalAccessToken::findToken($token);
                    if ($accessToken) {
                        $user = $accessToken->tokenable;
                    }
                }
                if (!$user) {
                    $user = \App\Models\User::where('api_token', $token)->first();
                }
                if ($user) {
                    auth()->login($user);
                }
            }
        }
        $isDeveloper = session('is_developer') || (auth()->check() && auth()->user() && auth()->user()->email === 'mikale@gmail.com');
        if ($isDeveloper && !session()->has('active_restaurant_id')) {
            if (!$request->routeIs('admin.developer.select_restaurant') && !$request->routeIs('admin.restaurants.select')) {
                return redirect()->route('admin.developer.select_restaurant');
            }
        }
        return $next($request);
    }
}
