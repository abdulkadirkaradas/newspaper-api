<?php

use App\Http\Middleware\CheckHeaders;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAuthorization;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Middleware\UserLoginMiddleware;
use App\Http\Middleware\UserRegisterMiddleware;
use App\Http\Controllers\v1\Users\LoginController;
use App\Http\Controllers\v1\Users\RegisterController;

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
        Route::post('/register', [RegisterController::class, 'register']);
    });

    Route::post('/login', [LoginController::class, 'login']);
});

Route::prefix('v1')->middleware([CheckAuthentication::class, CheckHeaders::class/*, CheckAuthorization::class*/])->group(function () {
    // Authentication Routes
    Route::prefix('auth')->group(function () {
        Route::middleware([UserLoginMiddleware::class])->group(function () {
            Route::post('/logout', [LoginController::class, 'logout'])
                ->withoutMiddleware([CheckHeaders::class]);
            Route::post('/refresh-auth-token', [LoginController::class, 'refreshAuthToken'])
                ->withoutMiddleware([CheckHeaders::class]);
        });
    });
});