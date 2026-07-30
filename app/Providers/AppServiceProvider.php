<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
use App\Models\Restaurant;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        View::composer('admin.*', function ($view) {
            if (Schema::hasTable('restaurants')) {
                $activeId = null;
                if (session()->has('active_restaurant_id')) {
                    $activeId = session('active_restaurant_id');
                }
                
                $activeRestaurant = null;
                if ($activeId) {
                    $activeRestaurant = Restaurant::find($activeId);
                }
                
                if (!auth()->check()) {
                    $token = request()->cookie('admin_token');
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
                
                if (!$activeRestaurant && !$isDeveloper) {
                    $activeRestaurant = Restaurant::first();
                    if ($activeRestaurant) {
                        session(['active_restaurant_id' => $activeRestaurant->id]);
                    }
                }
                
                $restaurants = Restaurant::all();
                $view->with('activeRestaurant', $activeRestaurant);
                $view->with('restaurantsList', $restaurants);
            }
        });
    }
}
