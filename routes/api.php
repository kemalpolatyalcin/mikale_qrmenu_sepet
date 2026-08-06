<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ErestIntegrationController;

Route::get('/products', [MenuController::class, 'getProducts']);
Route::get('/categories', [MenuController::class, 'getCategories']);
Route::get('/user', [MenuController::class, 'getUser']);
Route::get('/tables/{token}', [MenuController::class, 'getTable']);

Route::post('/admin/login', [AdminAuthController::class, 'login'])->middleware([
    \Illuminate\Cookie\Middleware\EncryptCookies::class,
    \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
    \Illuminate\Session\Middleware\StartSession::class,
]);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/admin/logout', [AdminAuthController::class, 'logout']);
    Route::post('/products', [AdminController::class, 'storeProduct']);
    Route::post('/products/{id}', [AdminController::class, 'updateProduct']);
    Route::delete('/products/{id}', [AdminController::class, 'deleteProduct']);
    Route::post('/categories', [AdminController::class, 'storeCategory']);
    Route::post('/categories/{id}', [AdminController::class, 'updateCategory']);
    Route::delete('/categories/{id}', [AdminController::class, 'deleteCategory']);
});

Route::prefix('erest')->group(function () {
    Route::get('/tables', [ErestIntegrationController::class, 'getTables']);
    Route::post('/orders', [ErestIntegrationController::class, 'receiveOrder']);
    Route::get('/reports', [ErestIntegrationController::class, 'getReports']);
});