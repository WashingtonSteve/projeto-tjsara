<?php

declare(strict_types=1);

namespace App\Application\Task\UseCases;

use App\Domain\Task\Entities\Task;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\Repositories\TaskRepositoryInterface;

final class FindTask
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
    ) {}

    public function __invoke(int $id): Task
    {
        return $this->tasks->find($id) ?? throw TaskNotFoundException::withId($id);
    }
}
