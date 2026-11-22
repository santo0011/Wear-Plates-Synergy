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
    ->withMiddleware(function (Middleware $middleware): void {
        // Unauthenticated visitors hitting admin routes go to the admin login page.
        $middleware->redirectGuestsTo(fn () => route('admin.login'));
        // Logged-in admins visiting the login page go to the dashboard.
        $middleware->redirectUsersTo(fn () => route('admin.dashboard'));

        $middleware->web(append: [\App\Http\Middleware\SanitizeUtf8Input::class]);

        $middleware->alias([
            'admin.active' => \App\Http\Middleware\EnsureAdminIsActive::class,
            'admin.nocache' => \App\Http\Middleware\PreventBackHistory::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
