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
use App\Http\Controllers\v1\Admin\AnnouncementsController;
use App\Http\Controllers\v1\Users\NewsController as UserNewsController;

Route::prefix('v1/admin')->middleware([
    CheckHeaders::class,
    CheckAuthentication::class,
    ValidateUUID::class,
    'role:Admin',
    'throttle:30,1',
    'api'
])->group(function () {

    Route::prefix('announcements')->group(function () {
        // Return announcements | priority, from, to, latest
        Route::get('/', [AnnouncementsController::class, 'index']);
        // Create an announcement
        Route::post('/', [AnnouncementsController::class, 'store']);
    });

    // User based function routes
    Route::prefix('user')->group(function () {
        // Return user informations | id, type['all, blocked']
        Route::get('/', [UsersController::class, 'index']);
        // Change user role
        Route::put('/{user}/role', [UsersController::class, 'updateRole']);
        // Block user
        Route::put('/{user}/block', [UsersController::class, 'block']);
    });

    Route::prefix('news')->group(function () {
        // Return news | id, type[all, approve, unapproved]
        Route::get('/', [NewsController::class, 'index']);

        // Return all news categories
        Route::get('/categories', [NewsController::class, 'categories']);

        // Approve news by user and news id
        Route::post('{news}/approve', [NewsController::class, 'approve']);

        // Create a new post
        Route::post('/', [UserNewsController::class, 'store'])->middleware([SanitizeHtmlContent::class]);

        // Create a new post image
        Route::post('{news}/images', [UserNewsController::class, 'uploadImage'])
            ->middleware([
                VerifyImageUploadHeader::class,
                VerifyNewsImagesFolderExists::class,
            ])
            ->withoutMiddleware([CheckHeaders::class]);

        // Create a news category
        Route::post('/categories', [NewsController::class, 'createCategory']);

        // Update a category of news record
        Route::put('/categories/{category}', [NewsController::class, 'updateCategory']);

        // Update approved news visibility
        Route::put('{news}/visibility', [NewsController::class, 'updateVisibility']);

        // Delete news by user and news id
        Route::delete('/{news}/delete', [NewsController::class, 'delete']);
    });

    // Notification based function routes
    Route::prefix('notifications')->group(function () {
        // Return user notifications|all, read, unread, time-range based
        Route::get('/', [UsersController::class, 'getUserNotifications']);

        // Create notification for the provided user
        Route::post('/{user}', [UsersController::class, 'createNotification']);
    });

    // Notification based function routes
    Route::prefix('warnings')->group(function () {
        // Return user warnings
        Route::get('/{user}', [UsersController::class, 'getUserWarnings']);

        // Return user warnings
        Route::post('/{user}', [UsersController::class, 'createWarning']);
    });

    Route::prefix('badges')->group(function () {
        // Create a new badge
        Route::post('/', [BadgesController::class, 'store']);
        // Create a new badge image
        Route::post('{badge}/image', [BadgesController::class, 'uploadImage'])
            ->middleware([
                VerifyImageUploadHeader::class,
                VerifyBadgesFolderExists::class,
            ])
            ->withoutMiddleware([CheckHeaders::class]);
    });

    Route::prefix('helpers')->group(function () {
        // Return default user roles
        Route::get('/user-roles', [HelpersController::class, 'userRoles']);
        // Return default warning levels
        Route::get('/warning-levels', [HelpersController::class, 'warningLevels']);
    });
});