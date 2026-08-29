<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;

Route::post('/login', [AuthController::class, 'login']);

// Public catalog endpoints (used by the storefront, no auth required)
Route::get('/catalog', [ProductController::class, 'grouped']);
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/products/by-category/{id}', [ProductController::class, 'showByCategory']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('categories', CategoryController::class)->except(['index']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResource('users', UserController::class);
    // Password change (User changes own password)
    Route::post('/users/change-password', [UserController::class, 'changePassword']);

    // Admin resets password
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword']);
});
