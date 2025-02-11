<?php

use App\Http\Controllers\v1\Public\NewsController;
use App\Http\Controllers\v1\Public\UserController;
use App\Http\Middleware\ValidateUUID;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/public')->middleware([ValidateUUID::class])->group(function () {
    Route::prefix('user')->group(function () {
        // Returns requested user informations
        Route::get('/', [UserController::class, 'user']);
    });

    Route::prefix('news')->group(function () {
        // Return all user news
        Route::get('/', [NewsController::class, 'news']);
        // Return all categories
        Route::get('/categories', [NewsController::class, 'categories']);
    });
});