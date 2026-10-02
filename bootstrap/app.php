<?php

use App\Http\Middleware\EnsureStaff;
use App\Http\Middleware\SetLocale;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [SetLocale::class]);

        $middleware->alias([
            'staff' => EnsureStaff::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

        // SSLCommerz posts back to us from their domain — no CSRF token there.
        $middleware->validateCsrfTokens(except: ['payment/sslcommerz/*']);

        $middleware->redirectGuestsTo(fn (Request $request) => $request->is('admin*') ? route('admin.login') : route('login'));
        $middleware->redirectUsersTo(fn (Request $request) => $request->user()?->isStaff() && $request->is('admin*') ? route('admin.dashboard') : route('account.dashboard'));
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
