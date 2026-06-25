<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Exceptions\UnauthorizedException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        commands: __DIR__ . '/../routes/console.php',
        using: function (): void {
            Route::middleware('api')
                ->prefix('api/v1/auth')
                ->name('apiAuth.')
                ->group(base_path('routes/apiAuth.php'));

            Route::middleware(['auth:sanctum', 'role:admin'])
                ->prefix('api/v1/admin')
                ->name('apiAdmin.')
                ->group(base_path('routes/apiAdmin.php'));

            Route::middleware('api')
                ->prefix('api/v1')
                ->name('api.')
                ->group(base_path('routes/api.php'));

                Route::middleware('api')
                ->prefix('api/v1/ia')
                ->name('apiIa.')
                ->group(base_path('routes/apiIa.php'));

            Route::middleware(['auth:sanctum',  'role:admin|employee'])
                ->prefix('api/v1/mobile')
                ->name('apiMobile.')
                ->group(base_path('routes/apiMobile.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json([
                'success' => false,
                'message' => 'No autenticado. Por favor inicia sesión.'
            ], 401);
        });
        $exceptions->render(function (ValidationException $e, Request $request) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $e->errors(),
            ], 422);
        });
    $exceptions->render(function (UnauthorizedException $e, Request $request) {
        return response()->json([
            'success' => false,
            'message' => 'No tienes permisos para realizar esta acción'
        ], 403);
    });
    })->create();
