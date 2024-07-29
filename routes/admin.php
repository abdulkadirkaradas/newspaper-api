<?php

use App\Http\Middleware\CheckHeaders;
use App\Http\Middleware\ValidateUUID;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyNewsExists;
use App\Http\Middleware\VerifyBadgeExists;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Middleware\SanitizeHtmlContent;
use App\Http\Middleware\VerifyImageUploadHeader;
use App\Http\Controllers\v1\Admin\NewsController;
use App\Http\Middleware\VerifyBadgesFolderExists;
use App\Http\Controllers\v1\Admin\UsersController;
use App\Http\Controllers\v1\Admin\BadgesController;
use App\Http\Controllers\v1\Admin\HelpersController;
use App\Http\Middleware\VerifyNewsImagesFolderExists;
use App\Http\Controllers\v1\Users\NewsController as UserNewsController;

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

        // Change user role
        Route::put('change-role', [UsersController::class, 'change_user_role']);
        // Block user
        Route::post('/block', [UsersController::class, 'block_user']);
    });

    Route::prefix('news')->group(function () {
        // Return news | id, type[all, approve, unapproved]
        Route::get('/', [NewsController::class, 'news']);

        // Approve news by user and news id
        Route::post('/approve', [NewsController::class, 'approve']);

        // Delete news by user and news id
        Route::post('/delete', [NewsController::class, 'delete']);

        // Create a new post
        Route::post('/create', [UserNewsController::class, 'create'])->middleware([SanitizeHtmlContent::class]);
        // Create a new post image
        Route::post('/upload-image', [UserNewsController::class, 'upload_news_image'])
            ->middleware([
                VerifyImageUploadHeader::class,
                VerifyNewsExists::class,
                VerifyNewsImagesFolderExists::class,
            ])
            ->withoutMiddleware([CheckHeaders::class]);

        // Update approved news visibility
        Route::put('/change-post-visibility', [NewsController::class, 'change_post_visibility']);
    });

    // Notification based function routes
    Route::prefix('notifications')->group(function () {
        // Return user notifications|all, read, unread, time-range based
        Route::get('/', [UsersController::class, 'get_user_notifications']);

        // Create notification for the provided user
        Route::post('/create', [UsersController::class, 'create_notification']);
    });

    // Notification based function routes
    Route::prefix('warnings')->group(function () {
        // Return user warnings
        Route::get('/', [UsersController::class, 'get_user_warnings']);

        // Return user warnings
        Route::post('/create', [UsersController::class, 'create_warning']);
    });

    Route::prefix('badges')->group(function () {
        // Create a new badge
        Route::post('/create', [BadgesController::class, 'create']);
        // Create a new badge image
        Route::post('/upload-image', [BadgesController::class, 'upload_image'])
            ->middleware([
                VerifyImageUploadHeader::class,
                VerifyBadgeExists::class,
                VerifyBadgesFolderExists::class,
            ])
            ->withoutMiddleware([CheckHeaders::class]);
    });

    Route::prefix('helpers')->group(function () {
        // Return default user roles
        Route::get('/user-roles', [HelpersController::class, 'user_roles']);
        // Return default warning levels
        Route::get('/warning-levels', [HelpersController::class, 'warning_levels']);
    });
});