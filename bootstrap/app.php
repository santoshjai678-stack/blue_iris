<?php

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
    ->withMiddleware(function (Middleware $middleware): void {
        // Exclude all API endpoints from CSRF verification
        $middleware->validateCsrfTokens(except: [
            'api/*',
            'api/v1/*',
            '*/api/*',
            '*/api/v1/*',
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            $centralDomains = config('tenancy.central_domains', ['localhost', '127.0.0.1']);
            $host = $request->getHost();

            // If we are on a tenant domain or tenant() is initialized
            if ((function_exists('tenant') && tenant()) || !in_array($host, $centralDomains)) {
                return route('tenant.login');
            }

            // Central domain superadmin
            return route('superadmin.login');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();