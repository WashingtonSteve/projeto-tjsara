<?php

declare(strict_types=1);

namespace App\Application\Task\UseCases;

use App\Domain\Task\Entities\Task;
use App\Domain\Task\Repositories\TaskRepositoryInterface;

final class ListTasks
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
    ) {}

    /**
     * @return list<Task>
     */
    public function __invoke(): array
    {
        return $this->tasks->all();
    }
}
