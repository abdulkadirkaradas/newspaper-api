<?php

use App\Http\Controllers\v1\Public\NewsController;
use App\Http\Controllers\v1\Public\UserController;
use App\Http\Middleware\ValidateUserAndNewsIDs;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/public')->group(function () {
    // Returns requested user informations
    Route::get('/user/{id}', [UserController::class, 'user'])->middleware(ValidateUserAndNewsIDs::class);
    // Return logged user news
    Route::get('/post/{id}', [NewsController::class, 'news'])->middleware(ValidateUserAndNewsIDs::class);
});