<?php

declare(strict_types=1);

namespace App\Application\Task\UseCases;

use App\Domain\Task\Entities\Task;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\Repositories\TaskRepositoryInterface;

final class RemoveSubtask
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
    ) {}

    public function __invoke(int $taskId, int $subtaskId): Task
    {
        $task = $this->tasks->find($taskId) ?? throw TaskNotFoundException::withId($taskId);

        $task->removeSubtask($subtaskId);

        return $this->tasks->save($task);
    }
}
