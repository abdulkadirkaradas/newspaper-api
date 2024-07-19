<?php

use App\Http\Middleware\CheckHeaders;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Middleware\RoleAdminMiddleware;
use App\Http\Controllers\v1\Admin\UsersController;

Route::prefix('v1/admin')->middleware([CheckHeaders::class, CheckAuthentication::class, RoleAdminMiddleware::class, 'throttle:30,1'])->group(function () {

    // User processes
    Route::prefix('user')->group(function () {
        // Return user informations | id, type['all, blocked']
        Route::get('/', [UsersController::class, 'user']);
        // Block user
        Route::post('/block', [UsersController::class, 'block_user']);
    });
});