<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\MenuController;

Route::get('/api/categories', [MenuController::class, 'getCategories']);
Route::get('/api/products', [MenuController::class, 'getProducts']);

Route::post('/api/orders/place', [OrderController::class, 'placeOrder']);

Route::get('/', function () {
    return view('welcome');
});

Route::get('/manager', function () {
    return view('manager');
});

Route::get('/admin', function () {
    return view('manager');
});

Route::get('/api/orders', [OrderController::class, 'index']);