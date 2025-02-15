<?php

use App\Http\Middleware\CheckHeaders;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Middleware\UserRegisterMiddleware;
use App\Http\Controllers\v1\Users\LoginController;
use App\Http\Controllers\v1\Users\RegisterController;

/**
 * Admin authentication routes
 */
Route::prefix('v1/admin')->group(function () {
    // Authentication Routes
    Route::prefix('auth')->group(function () { });
});

/**
 * User authentication routes
 */
Route::prefix('v1')->middleware([
    CheckHeaders::class,
])->group(function () {
    Route::prefix('auth')->group(function () {
        // User registration
        Route::post('/register', [RegisterController::class, 'register'])
            ->middleware([UserRegisterMiddleware::class]);

        // User login
        Route::post('/login', [LoginController::class, 'login']);

        // Protected routes
        Route::middleware([CheckAuthentication::class])->group(function () {
            Route::post('/logout', [LoginController::class, 'logout']);
            Route::post('/refresh-auth-token', [LoginController::class, 'refreshAuthToken']);
            Route::get('/user', [LoginController::class, 'userInformation']);
        });
    });
});
