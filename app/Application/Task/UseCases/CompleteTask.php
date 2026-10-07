<?php

declare(strict_types=1);

namespace App\Application\Task\UseCases;

use App\Application\Contracts\EventDispatcherInterface;
use App\Domain\Task\Entities\Task;
use App\Domain\Task\Events\TaskCompleted;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\Repositories\TaskRepositoryInterface;

final class CompleteTask
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
        private readonly EventDispatcherInterface $events,
    ) {}

    public function __invoke(int $id): Task
    {
        $task = $this->tasks->find($id) ?? throw TaskNotFoundException::withId($id);

        $task->complete();

        $completed = $this->tasks->save($task);

        $this->events->dispatch(new TaskCompleted(
            taskId: $completed->id() ?? $id,
            title: $completed->title(),
            completedAt: $completed->updatedAt(),
        ));

        return $completed;
    }
}
