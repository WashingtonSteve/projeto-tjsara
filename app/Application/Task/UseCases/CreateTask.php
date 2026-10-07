<?php

declare(strict_types=1);

namespace App\Application\Task\UseCases;

use App\Application\Task\DataTransferObjects\CreateTaskData;
use App\Domain\Task\Entities\Task;
use App\Domain\Task\Repositories\TaskRepositoryInterface;

final class CreateTask
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
    ) {}

    public function __invoke(CreateTaskData $data): Task
    {
        $task = Task::create($data->title, $data->description, $data->dueDate);

        return $this->tasks->save($task);
    }
}
