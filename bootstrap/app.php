<?php

use App\Domain\Task\Exceptions\InvalidTaskDataException;
use App\Domain\Task\Exceptions\TaskAlreadyCompletedException;
use App\Domain\Task\Exceptions\TaskNotFoundException;
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
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (TaskNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        });

        $exceptions->render(function (TaskAlreadyCompletedException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        });

        $exceptions->render(function (InvalidTaskDataException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        });
    })->create();
