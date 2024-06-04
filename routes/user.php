<?php

use App\Http\Controllers\v1\Users\NotificationsController;
use App\Http\Middleware\CheckHeaders;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Controllers\v1\Users\UsersController;

/**
 * 'Writer' routes
 */
Route::prefix('v1')->middleware([CheckAuthentication::class, CheckHeaders::class])->group(function () {
    //
    Route::prefix('user')->group(function () {
        Route::get('/profile', [UsersController::class, 'profile']);
    });

    Route::prefix('notifications')->group(function () {
        // Returns all unread notifications
        Route::get('/', [NotificationsController::class, 'notifications']);
    });
});