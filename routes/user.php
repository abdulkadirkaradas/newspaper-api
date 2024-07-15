<?php

use App\Http\Middleware\CheckHeaders;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\VerifyNewsExists;
use App\Http\Middleware\CheckAuthentication;
use App\Http\Middleware\SanitizeHtmlContent;
use App\Http\Controllers\v1\Users\NewsController;
use App\Http\Controllers\v1\Users\UsersController;
use App\Http\Middleware\VerifyImageUploadHeader;

/**
 * 'Writer' routes
 */
Route::prefix('v1/writer')->middleware([CheckHeaders::class, CheckAuthentication::class, 'throttle:20,1'])->group(function () {

    // Returns logged user informations
    Route::get('/profile', [UsersController::class, 'profile']);

    Route::prefix('news')->group(function () {
        // Return news by related id
        Route::get('/logged-user-news', [NewsController::class, 'logged_user_news']);
        // Return all news reactions
        Route::get('/reactions', [NewsController::class, 'reactions']);
        // Create a new post
        Route::post('/create', [NewsController::class, 'create'])->middleware([SanitizeHtmlContent::class]);
        // Create a new post image
        Route::post('/upload-image', [NewsController::class, 'upload_news_image'])
            ->middleware([
                VerifyImageUploadHeader::class,
                VerifyNewsExists::class
            ])
            ->withoutMiddleware([CheckHeaders::class]);
    });

    // All routes support the return of notifications based on a time-range (optional).
    Route::prefix('notifications')->group(function () {
        // Returns all unread notifications
        Route::get('/', [UsersController::class, 'notifications']);
    });

    Route::prefix('warnings')->group(function () {
        Route::get('/', [UsersController::class, 'warnings']);
    });
});