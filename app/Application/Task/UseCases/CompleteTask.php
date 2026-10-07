<?php

declare(strict_types=1);

namespace App\Application\Task\UseCases;

use App\Domain\Task\Entities\Task;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\Repositories\TaskRepositoryInterface;

final class CompleteTask
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
    ) {}

    public function __invoke(int $id): Task
    {
        $task = $this->tasks->find($id) ?? throw TaskNotFoundException::withId($id);

        $task->complete();

        return $this->tasks->save($task);
    }
}
