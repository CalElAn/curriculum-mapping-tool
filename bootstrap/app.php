<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Inertia\Inertia;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(
            append: [
                \App\Http\Middleware\HandleInertiaRequests::class,
                \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
            ],
        );

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' =>
                \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' =>
                \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (
            Response $response,
            Throwable $exception,
            Request $request,
        ) {
            /* adapted from https://inertiajs.com/error-handling
                This works without throwing this error: "All Inertia requests must receive a valid Inertia response,
                however a plain JSON response was received." because of a check in "app.ts" that prevents the default event behaviour
                */
            if ($response->getStatusCode() === 403 && $request->inertia()) {
                return response()->json(
                    [
                        'message' => $exception->getMessage(),
                    ],
                    403,
                );
            }

            return $response;
        });
    })
    ->create();
