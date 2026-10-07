<?php

declare(strict_types=1);

namespace App\Application\Task\UseCases;

use App\Domain\Task\Entities\Task;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\Repositories\TaskRepositoryInterface;

final class DetachTag
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
    ) {}

    public function __invoke(int $taskId, int $tagId): Task
    {
        $task = $this->tasks->find($taskId) ?? throw TaskNotFoundException::withId($taskId);

        $task->detachTag($tagId);

        return $this->tasks->save($task);
    }
}
