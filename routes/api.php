<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\ErestIntegrationController;

Route::get('/products', [MenuController::class, 'getProducts']);
Route::get('/categories', [MenuController::class, 'getCategories']);
Route::get('/user', [MenuController::class, 'getUser']);
Route::get('/tables/{token}', [MenuController::class, 'getTable']);

Route::prefix('erest')->group(function () {
    Route::get('/tables', [ErestIntegrationController::class, 'getTables']);
    Route::post('/orders', [ErestIntegrationController::class, 'receiveOrder']);
    Route::get('/reports', [ErestIntegrationController::class, 'getReports']);
});