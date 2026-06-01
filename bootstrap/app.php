<?php

require_once __DIR__.'/../app/Support/SortDirection.php';

use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);
        $middleware->web(replace: [
            PreventRequestForgery::class => ValidateCsrfToken::class,
        ]);
        $middleware->redirectGuestsTo(
            fn ($request) => $request->is('espace-adherent*') ? route('adherent.login') : route('login'),
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
