<?php

use App\Http\Middleware\CheckHeaders;
use App\Http\Middleware\ValidateUUID;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Controllers\v1\Admin\UsersController;

Route::prefix('v1/admin')->middleware([
    CheckHeaders::class,
    CheckAuthentication::class,
    ValidateUUID::class,
    'role:Admin',
    'throttle:30,1'
])->group(function () {

    // User based function routes
    Route::prefix('user')->group(function () {
        // Return user informations | id, type['all, blocked']
        Route::get('/', [UsersController::class, 'user']);
        // Return user warnings
        Route::get('warnings', [UsersController::class, 'get_user_warnings']);

        // Block user
        Route::post('/block', [UsersController::class, 'block_user']);
    });

    // Notification based function routes
    Route::prefix('notifications')->group(function () {
        // Return user notifications|all, read, unread, time-range based
        Route::get('/', [UsersController::class, 'get_user_notifications']);

        // Create notification for the provided user
        Route::post('/create', [UsersController::class, 'create_notification']);
    });
});