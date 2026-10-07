<?php

declare(strict_types=1);

namespace App\Domain\Task\Repositories;

use App\Domain\Task\Entities\Task;

interface TaskRepositoryInterface
{
    /**
     * @return list<Task>
     */
    public function all(): array;

    public function find(int $id): ?Task;

    /**
     * Persists the task, creating it when it has no identity yet, and returns
     * the persisted entity (with its ID populated for newly created tasks).
     */
    public function save(Task $task): Task;

    public function delete(int $id): void;
}
