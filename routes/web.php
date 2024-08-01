<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAuthentication;

Route::prefix('')->middleware([CheckAuthentication::class])->group(function () {
    Route::get('/', function () {});
});