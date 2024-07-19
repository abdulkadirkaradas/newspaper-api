<?php

use App\Http\Middleware\CheckHeaders;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Middleware\RoleAdminMiddleware;
use App\Http\Controllers\v1\Admin\UsersController;
use App\Http\Middleware\ValidateUUID;

Route::prefix('v1/admin')->middleware([CheckHeaders::class, CheckAuthentication::class, RoleAdminMiddleware::class, 'throttle:30,1'])->group(function () {

    // User processes
    Route::prefix('user')->group(function () {
        //--------------------------------------------------------------------//
        //                          GET Routes                                //
        //--------------------------------------------------------------------//

        // Return user informations | id, type['all, blocked']
        Route::get('/', [UsersController::class, 'user'])
            ->middleware([ValidateUUID::class]);
        // Return user notifications|all, read, unread, time-range based
        Route::get('notifications', [UsersController::class, 'notifications'])
            ->middleware([ValidateUUID::class]);
        // Return user warnings
        Route::get('warnings', [UsersController::class, 'warnings'])
            ->middleware([ValidateUUID::class]);

        //--------------------------------------------------------------------//
        //                          POST Routes                               //
        //--------------------------------------------------------------------//

        // Block user
        Route::post('/block', [UsersController::class, 'block_user'])
            ->middleware([ValidateUUID::class]);
    });
});