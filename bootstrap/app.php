<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\Cors;
// use App\Http\Middleware\VerifyCsrfToken;
// use App\Http\Middleware\EnsureJWTTokenIsValid;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->append(Cors::class);
        // $middleware->append(EnsureJWTTokenIsValid::class);
        // $middleware->validateCsrfTokens(except: [
        //     '/foo/*',
        //     '/*',
        // ]);
        // $middleware->append(VerifyCsrfToken::class);
        // $middleware->validateCsrfTokens(except: [
        //     'stripe/*',
        //     'http://localhost:5173/*',
        //     'http://example.com/foo/*',
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
