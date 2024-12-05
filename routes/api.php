<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\StatusController;
use App\Http\Middleware\JWTMiddleware;

Route::get('/orders', [OrderController::class, 'index']);
Route::post('/sign-up', [UserController::class, 'register']);
Route::get('/status', [StatusController::class, 'index']);

Route::middleware([JWTMiddleware::class])->group(function() {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::post('/order/update-status', [StatusController::class, 'updateStatus']);
});
