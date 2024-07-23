<?php

use App\Http\Middleware\CheckHeaders;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Middleware\UserLoginMiddleware;
use App\Http\Middleware\UserRegisterMiddleware;
use App\Http\Controllers\v1\Users\LoginController as UsersLoginController;
use App\Http\Controllers\v1\Users\RegisterController as UsersRegisterController;

/**
 * Admin authentication routes
*/
Route::prefix('v1/admin')->group(function () {
    // Authentication Routes
    Route::prefix('auth')->group(function () {});
});

/**
 * User authentication routes
*/
Route::prefix('v1/auth')->middleware([CheckHeaders::class])->group(function () {
    Route::middleware([UserRegisterMiddleware::class])->group(function () {
        Route::post('/register', [UsersRegisterController::class, 'register']);
    });

    Route::post('/login', [UsersLoginController::class, 'login']);
});

Route::prefix('v1')->middleware([CheckAuthentication::class, CheckHeaders::class/*, CheckAuthorization::class*/])->group(function () {
    // Authentication Routes
    Route::prefix('auth')->group(function () {
        Route::middleware([UserLoginMiddleware::class])->group(function () {
            Route::post('/logout', [UsersLoginController::class, 'logout']);
            Route::post('/refresh-auth-token', [UsersLoginController::class, 'refreshAuthToken']);
        });
    });
});