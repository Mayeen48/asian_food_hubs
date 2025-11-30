<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::apiResource('products', ProductController::class);
    Route::get('/products/by-category/{id}', [ProductController::class, 'showByCategory']);
    Route::apiResource('categories', CategoryController::class);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('users', UserController::class);
    // Password change (User changes own password)
    Route::post('/users/change-password', [UserController::class, 'changePassword']);

    // Admin resets password
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword']);
});
Route::get('/catalog', [ProductController::class, 'grouped']);
