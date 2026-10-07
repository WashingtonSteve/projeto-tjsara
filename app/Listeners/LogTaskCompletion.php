<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Domain\Task\Events\TaskCompleted;
use App\Models\ActivityLog;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Runs on the queue, off the request/response cycle, so a slow or failing
 * side effect of completing a task never delays the API response.
 */
final class LogTaskCompletion implements ShouldQueue
{
    public function handle(TaskCompleted $event): void
    {
        ActivityLog::query()->create([
            'description' => "Task \"{$event->title}\" (#{$event->taskId}) was completed.",
        ]);
    }
}
