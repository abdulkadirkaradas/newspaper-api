<?php

use App\Http\Controllers\v1\Users\NewsController;
use App\Http\Controllers\v1\Users\UsersController;
use App\Http\Middleware\CheckNewsId;
use App\Http\Middleware\CheckUserId;
use App\Http\Middleware\ValidateUserAndNewsIDs;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/common')->group(function () {
    // Returns requested user informations
    Route::get('/user/{id}', [UsersController::class, 'user'])->middleware(ValidateUserAndNewsIDs::class);
    // Return logged user news
    Route::get('/post/{id}', [NewsController::class, 'news'])->middleware(ValidateUserAndNewsIDs::class);
});