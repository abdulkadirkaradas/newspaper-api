<?php

use App\Http\Controllers\v1\RegisterController;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Middleware\CheckAuthorization;
use App\Http\Middleware\RegisterMiddleware;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->middleware([CheckAuthentication::class/*, CheckAuthorization::class*/])->group(function () {

    Route::middleware([RegisterMiddleware::class])->group(function () {
        Route::post('/register', [RegisterController::class, 'register']);
    });
});