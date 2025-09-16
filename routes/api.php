<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\CartController;
use App\Http\Controllers\Api\V1\CategoryController;

Route::prefix('v1')->group(function () {
    
    // Registration and Login Routes
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);

    // Get All Products
    Route::get('/products', [ProductController::class, 'index']);

    // Middleware for Authenticated Users
    Route::middleware('auth:sanctum')->group(function () {

        // Admin Routes Only
        Route::middleware('admin')->group(function () {
            Route::get('/products/deleted', [ProductController::class, 'deleted']);
            Route::post('/products', [ProductController::class, 'store']);
            Route::put('/products/{id}', [ProductController::class, 'update']);
            Route::delete('/products/{id}', [ProductController::class, 'destroy']);
            Route::post('/products/{id}/restore', [ProductController::class, 'restore']);
        
            Route::get('/categories', [CategoryController::class, 'index']);
            Route::post('/categories', [CategoryController::class, 'store']);
            Route::put('/categories/{id}', [CategoryController::class, 'update']);
        });

        // Cart Routes
        Route::get('/cart', [CartController::class, 'index']);
        Route::post('/cart', [CartController::class, 'store']);
        Route::put('/cart/{product_id}', [CartController::class, 'update']);
        Route::delete('/cart/{product_id}', [CartController::class, 'destroy']);

    });
});
