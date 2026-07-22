<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return view('welcome');
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
});

Route::get('/api/categories', [MenuController::class, 'getCategories']);
Route::get('/api/products', [MenuController::class, 'getProducts']);
Route::get('/api/tables/{token}', [MenuController::class, 'getTable']);

Route::post('/api/orders/place', [OrderController::class, 'placeOrder']);

Route::get('/manager', function () {
    return view('manager');
});

Route::get('/api/orders', [OrderController::class, 'index']);