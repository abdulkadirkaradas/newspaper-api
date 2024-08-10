<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckAuthentication;

Route::middleware([CheckAuthentication::class])->group(function () {
    Route::get('/', function () {});
});