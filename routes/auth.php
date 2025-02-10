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
    Route::prefix('auth')->group(function () { });
});

/**
 * User authentication routes
 */
Route::prefix('v1')->middleware([CheckHeaders::class])->group(function () {
    Route::prefix('auth')->group(function () {
        // User registration
        Route::post('/register', [UsersRegisterController::class, 'register'])
            ->middleware([UserRegisterMiddleware::class]);

        // User login
        Route::post('/login', [UsersLoginController::class, 'login']);

        // Protected routes
        Route::middleware([UserLoginMiddleware::class, CheckAuthentication::class])->group(function () {
            Route::post('/logout', [UsersLoginController::class, 'logout']);
            Route::post('/refresh-auth-token', [UsersLoginController::class, 'refreshAuthToken']);
            Route::get('/user', [UsersLoginController::class, 'userInformation']);
        });
    });
});
