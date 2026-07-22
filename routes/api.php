<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;

Route::get('/products', [MenuController::class, 'getProducts']);
Route::get('/categories', [MenuController::class, 'getCategories']);
Route::get('/user', [MenuController::class, 'getUser']);
Route::get('/tables/{token}', [MenuController::class, 'getTable']);