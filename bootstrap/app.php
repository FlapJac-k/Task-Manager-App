<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (NotFoundHttpException $e, $request): JsonResponse {
            return response()->json([
                'message' => 'The requested route was not found.',
                'errors' => [
                    'route' => ['The requested route was not found.']
                ]
            ], 404);
        });

        $exceptions->render(function (UnauthorizedException $e, $request): JsonResponse {
            return response()->json([
                'message' => 'Access denied. You do not have the required permissions.',
                'errors' => [
                    'authorization' => ['You do not have the required permissions.']
                ]
            ], 403);
        });
    })
    ->create();
