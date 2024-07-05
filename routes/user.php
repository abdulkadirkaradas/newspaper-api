<?php

use App\Http\Middleware\CheckNewsId;
use App\Http\Middleware\CheckUserId;
use App\Http\Middleware\CheckHeaders;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Controllers\v1\Users\NewsController;
use App\Http\Controllers\v1\Users\UsersController;
use App\Http\Controllers\v1\Users\NotificationsController;

/**
 * 'Writer' routes
 */
Route::prefix('v1/writer')->middleware([CheckAuthentication::class, CheckHeaders::class])->group(function () {

    // Returns requested user informations
    Route::get('/user/{id}', [UsersController::class, 'user'])->middleware(CheckUserId::class);
    // Returns logged user informations
    Route::get('/profile', [UsersController::class, 'profile']);

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