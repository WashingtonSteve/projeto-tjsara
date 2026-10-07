<?php

use App\Domain\Tag\Exceptions\InvalidTagDataException;
use App\Domain\Task\Exceptions\InvalidTaskDataException;
use App\Domain\Task\Exceptions\SubtaskNotFoundException;
use App\Domain\Task\Exceptions\TagNotAttachedException;
use App\Domain\Task\Exceptions\TaskAlreadyCompletedException;
use App\Domain\Task\Exceptions\TaskHasPendingSubtasksException;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

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
        // This is an API-only application: every response under /api must be
        // JSON, even when the client omits "Accept: application/json" or hits
        // a route that doesn't exist (which never reaches route middleware).
        $exceptions->shouldRenderJsonWhen(function (Request $request) {
            return $request->is('api/*');
        });

        $exceptions->render(function (TaskNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        });

        $exceptions->render(function (SubtaskNotFoundException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        });

        $exceptions->render(function (TaskAlreadyCompletedException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        });

        $exceptions->render(function (TaskHasPendingSubtasksException $e) {
            return response()->json(['message' => $e->getMessage()], 409);
        });

        $exceptions->render(function (TagNotAttachedException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        });

        $exceptions->render(function (InvalidTaskDataException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        });

        $exceptions->render(function (InvalidTagDataException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        });
    })->create();
