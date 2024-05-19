<?php

use App\Http\Controllers\v1\RegisterController;
use App\Http\Middleware\CheckAuthorization;
use Illuminate\Support\Facades\Route;

Route::prefix('v1/auth')->middleware([CheckAuthorization::class])->group(function () {
    Route::post('/register', [RegisterController::class, 'register']);
});