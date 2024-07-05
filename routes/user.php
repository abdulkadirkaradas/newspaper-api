<?php

use App\Http\Controllers\v1\Users\NotificationsController;
use App\Http\Middleware\CheckHeaders;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Controllers\v1\Users\UsersController;
use App\Http\Middleware\CheckUserId;

/**
 * 'Writer' routes
 */
Route::prefix('v1')->middleware([CheckAuthentication::class, CheckHeaders::class])->group(function () {
    //
    Route::prefix('user')->group(function () {
        Route::get('/{id}', [UsersController::class, 'user'])->middleware(CheckUserId::class);
        Route::get('/profile', [UsersController::class, 'profile']);
    });

    // All routes support the return of notifications based on a time-range (optional).
    Route::prefix('notifications')->group(function () {
        // Returns all unread notifications
        Route::get('/all', [NotificationsController::class, 'notifications']);
        // Returns all notifications
        Route::get('/read', [NotificationsController::class, 'notifications']);
        // Returns only readed notifications
        Route::get('/unread', [NotificationsController::class, 'notifications']);
    });
});