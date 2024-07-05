<?php

use App\Http\Middleware\CheckHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            App\Http\Middleware\CheckAuthentication::class,
            App\Http\Middleware\CheckAuthorization::class,
            App\Http\Middleware\CheckHeaders::class,
            App\Http\Middleware\CheckUserId::class,
            App\Http\Middleware\CheckNewsId::class,
            App\Http\Middleware\UserRegisterMiddleware::class,
            App\Http\Middleware\UserLoginMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
