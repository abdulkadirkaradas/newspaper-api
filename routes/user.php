<?php

use App\Http\Controllers\v1\Users\UsersController;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Middleware\CheckHeaders;
use Illuminate\Support\Facades\Route;

/**
 * User routes
 */
Route::prefix('v1/user')->middleware([CheckAuthentication::class, CheckHeaders::class])->group(function () {
    Route::get('/profile', [UsersController::class, 'profile']);
});