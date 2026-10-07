<?php

declare(strict_types=1);

use App\Http\Controllers\Api\AuthTokenController;
use App\Http\Controllers\Api\SubtaskController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\TaskTagController;
use Illuminate\Support\Facades\Route;

Route::post('/auth/token', [AuthTokenController::class, 'store'])
    ->middleware('throttle:login')
    ->name('auth.token');

Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {
    Route::delete('/auth/token', [AuthTokenController::class, 'destroy'])->name('auth.token.destroy');

    Route::prefix('tags')->group(function () {
        Route::get('/', [TagController::class, 'index'])->name('tags.index');
        Route::post('/', [TagController::class, 'store'])->name('tags.store');
    });

    Route::prefix('tasks')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
        Route::post('/', [TaskController::class, 'store'])->name('tasks.store');
        Route::get('/{task}', [TaskController::class, 'show'])->whereNumber('task')->name('tasks.show');
        Route::put('/{task}', [TaskController::class, 'update'])->whereNumber('task')->name('tasks.update');
        Route::delete('/{task}', [TaskController::class, 'destroy'])->whereNumber('task')->name('tasks.destroy');
        Route::post('/{task}/complete', [TaskController::class, 'complete'])->whereNumber('task')->name('tasks.complete');

        Route::prefix('/{task}/subtasks')->whereNumber('task')->group(function () {
            Route::post('/', [SubtaskController::class, 'store'])->name('tasks.subtasks.store');
            Route::post('/{subtask}/complete', [SubtaskController::class, 'complete'])->whereNumber('subtask')->name('tasks.subtasks.complete');
            Route::delete('/{subtask}', [SubtaskController::class, 'destroy'])->whereNumber('subtask')->name('tasks.subtasks.destroy');
        });

        Route::prefix('/{task}/tags')->whereNumber('task')->group(function () {
            Route::post('/', [TaskTagController::class, 'store'])->name('tasks.tags.store');
            Route::delete('/{tag}', [TaskTagController::class, 'destroy'])->whereNumber('tag')->name('tasks.tags.destroy');
        });
    });
});
