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
                if (!$activeRestaurant) {
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
