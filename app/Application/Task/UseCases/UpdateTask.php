<?php

declare(strict_types=1);

namespace App\Application\Task\UseCases;

use App\Application\Task\DataTransferObjects\UpdateTaskData;
use App\Domain\Task\Entities\Task;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\Repositories\TaskRepositoryInterface;

final class UpdateTask
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
    ) {}

    public function __invoke(int $id, UpdateTaskData $data): Task
    {
        $task = $this->tasks->find($id) ?? throw TaskNotFoundException::withId($id);

        $task->update($data->title, $data->description, $data->dueDate);

        return $this->tasks->save($task);
    }
}
