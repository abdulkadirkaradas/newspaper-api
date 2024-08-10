<?php

use App\Http\Controllers\v1\Public\NewsController;
use App\Http\Controllers\v1\Public\UserController;
use App\Http\Middleware\ValidateUUID;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/public')->middleware([ValidateUUID::class])->group(function () {
    // Returns requested user informations
    Route::get('/user', [UserController::class, 'user']);
    // Return logged user news
    Route::get('/news', [NewsController::class, 'news']);
});