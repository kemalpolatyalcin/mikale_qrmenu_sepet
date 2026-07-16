<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\OrderController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/api/orders', [OrderController::class, 'index']);