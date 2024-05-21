<?php

use App\Http\Controllers\v1\LoginController;
use App\Http\Controllers\v1\RegisterController;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Middleware\CheckAuthorization;
use App\Http\Middleware\LoginMiddleware;
use App\Http\Middleware\RegisterMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware([CheckAuthentication::class/*, CheckAuthorization::class*/])->group(function () {

    // Authentication Routes
    Route::prefix('auth')->group(function () {
        Route::middleware([RegisterMiddleware::class])->group(function () {
            Route::post('/register', [RegisterController::class, 'register']);
        });

        Route::middleware([LoginMiddleware::class])->group(function () {
            Route::post('/login', [LoginController::class, 'login']);
            Route::post('/refresh-auth-token', [LoginController::class, 'refreshAuthToken']);
        });
    });
});