<?php

use App\Http\Middleware\CheckHeaders;
use App\Http\Middleware\ValidateUUID;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyNewsExists;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Middleware\SanitizeHtmlContent;
use App\Http\Middleware\VerifyImageUploadHeader;
use App\Http\Controllers\v1\Users\NewsController;
use App\Http\Controllers\v1\Users\UsersController;
use App\Http\Controllers\v1\Users\BadgesController;
use App\Http\Middleware\VerifyNewsImagesFolderExists;

/**
 * 'Writer' routes
 */
Route::prefix('v1/writer')->middleware([
    CheckHeaders::class,
    CheckAuthentication::class,
    'role:Admin,Moderator,Writer',
    'throttle:20,1'
])->group(function () {

    // Returns logged user informations
    Route::get('/profile', [UsersController::class, 'profile']);

    Route::prefix('notifications')->group(function () {
        // Returns all notifications|all, read, unread, time-range based
        Route::get('/', [UsersController::class, 'notifications']);
    });

    Route::prefix('warnings')->group(function () {
        Route::get('/', [UsersController::class, 'warnings']);
    });

    Route::prefix('badges')->group(function () {
        Route::get('/', [BadgesController::class, 'badges']);
    });

    Route::prefix('news')->group(function () {
        // Return news by related id
        Route::get('/logged-user-news', [NewsController::class, 'logged_user_news']);
        // Return all news reactions
        Route::get('/reactions', [NewsController::class, 'reactions']);
        // Create a new post
        Route::post('/create', [NewsController::class, 'create'])->middleware([SanitizeHtmlContent::class]);
        // Create opposition to an existing news
        Route::post('/create-opposition', [NewsController::class, 'create_opposition'])
            ->middleware([
                SanitizeHtmlContent::class,
                ValidateUUID::class,
            ]);
        // Create a new post image
        Route::post('/{news}/image', [NewsController::class, 'uploadImage'])
            ->middleware([
                VerifyImageUploadHeader::class,
                VerifyNewsImagesFolderExists::class,
            ])
            ->withoutMiddleware([CheckHeaders::class]);
    });
});