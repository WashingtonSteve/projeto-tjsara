<?php

declare(strict_types=1);

use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::prefix('tasks')->group(function () {
    Route::get('/', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/{task}', [TaskController::class, 'show'])->whereNumber('task')->name('tasks.show');
    Route::put('/{task}', [TaskController::class, 'update'])->whereNumber('task')->name('tasks.update');
    Route::delete('/{task}', [TaskController::class, 'destroy'])->whereNumber('task')->name('tasks.destroy');
    Route::post('/{task}/complete', [TaskController::class, 'complete'])->whereNumber('task')->name('tasks.complete');
});
