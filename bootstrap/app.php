<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust all proxies — required when behind Coolify/Traefik reverse proxy
        // so Laravel detects HTTPS correctly and generates https:// asset URLs
        $middleware->trustProxies(at: '*');

        $middleware->validateCsrfTokens(except: [
            'sslcommerz/*',
        ]);
        $middleware->web(prepend: [
            \App\Http\Middleware\HandleRedirects::class,
        ]);
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\TrackVisitor::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
