<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

Route::get('/', function (\Illuminate\Http\Request $request) {
    $tableToken = $request->query('masa') ?? $request->query('table');
    $restaurant = null;
    if ($tableToken) {
        $table = \App\Models\Table::where('token', $tableToken)->first();
        if ($table) {
            $restaurant = \App\Models\Restaurant::find($table->restaurant_id);
            
            $sessionKey = 'table_session_' . $table->id;
            if (!session()->has($sessionKey) || session($sessionKey) !== $table->active_session_id) {
                \Darryldecode\Cart\Facades\CartFacade::clear();
                session([$sessionKey => $table->active_session_id]);
            }
        }
    }
    if (!$restaurant) {
        $restaurant = \App\Models\Restaurant::first();
    }

    $siteSettings = [];
    if ($restaurant) {
        $siteSettings = \App\Models\Setting::where('restaurant_id', $restaurant->id)->pluck('value', 'key')->toArray();
        if (empty($siteSettings['restaurant_name'])) {
            $siteSettings['restaurant_name'] = $restaurant->name;
        }
        if (empty($siteSettings['logo'])) {
            $siteSettings['logo'] = $restaurant->logo_url;
        }
        if (empty($siteSettings['cover_image'])) {
            $siteSettings['cover_image'] = $restaurant->cover_image_url;
        }
        if (empty($siteSettings['phone'])) {
            $siteSettings['phone'] = $restaurant->phone;
        }
        if (empty($siteSettings['address'])) {
            $siteSettings['address'] = $restaurant->address;
        }
    } else {
        $siteSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
    }

    return view('welcome', compact('siteSettings'));
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    Route::get('/', [AdminController::class, 'index'])->name('dashboard');

    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');

    Route::get('/categories', [AdminController::class, 'categories'])->name('categories');
    Route::post('/categories/store', [AdminController::class, 'storeCategory'])->name('categories.store');
    Route::post('/categories/update/{id}', [AdminController::class, 'updateCategory'])->name('categories.update');
    Route::get('/categories/delete/{id}', [AdminController::class, 'deleteCategory'])->name('categories.delete');

    Route::get('/products', [AdminController::class, 'products'])->name('products');
    Route::post('/products/store', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::post('/products/update/{id}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::get('/products/delete/{id}', [AdminController::class, 'deleteProduct'])->name('products.delete');

    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');

    Route::get('/tables', [AdminController::class, 'tables'])->name('tables');
    Route::post('/tables', [AdminController::class, 'storeTable'])->name('tables.store');
    Route::delete('/tables/{id}', [AdminController::class, 'deleteTable'])->name('tables.delete');
    Route::post('/tables/reset/{id}', [AdminController::class, 'resetTable'])->name('tables.reset');

    Route::post('/restaurants/select', [AdminController::class, 'selectRestaurant'])->name('restaurants.select');
});

Route::name('admin.')->group(function () {
    Route::get('/developer/restaurants', [AdminController::class, 'developerRestaurants'])->name('developer.restaurants');
    Route::post('/developer/restaurants/store', [AdminController::class, 'storeRestaurant'])->name('restaurants.store');
    Route::post('/developer/restaurants/update/{id}', [AdminController::class, 'updateRestaurant'])->name('restaurants.update');
    Route::get('/developer/restaurants/delete/{id}', [AdminController::class, 'deleteRestaurant'])->name('restaurants.delete');
});

Route::get('/api/categories', [MenuController::class, 'getCategories']);
Route::get('/api/products', [MenuController::class, 'getProducts']);
Route::get('/api/tables/{token}', [MenuController::class, 'getTable']);

Route::post('/api/orders/place', [OrderController::class, 'placeOrder']);

Route::get('/manager', function () {
    return view('manager');
});

Route::get('/api/orders', [OrderController::class, 'index']);