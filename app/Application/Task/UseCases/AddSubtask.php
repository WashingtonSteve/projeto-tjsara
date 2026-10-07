<?php

declare(strict_types=1);

namespace App\Application\Task\UseCases;

use App\Domain\Task\Entities\Task;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\Repositories\TaskRepositoryInterface;

final class AddSubtask
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
    ) {}

    public function __invoke(int $taskId, string $title): Task
    {
        $task = $this->tasks->find($taskId) ?? throw TaskNotFoundException::withId($taskId);

        $task->addSubtask($title);

        return $this->tasks->save($task);
    }
}
