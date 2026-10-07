<?php

declare(strict_types=1);

namespace App\Application\Task\UseCases;

use App\Domain\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\Repositories\TaskRepositoryInterface;

final class DeleteTask
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
    ) {}

    public function __invoke(int $id): void
    {
        $this->tasks->find($id) ?? throw TaskNotFoundException::withId($id);

        $this->tasks->delete($id);
    }
}
