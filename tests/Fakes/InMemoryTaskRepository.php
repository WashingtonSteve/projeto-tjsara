<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Domain\Task\Entities\Task;
use App\Domain\Task\Repositories\TaskRepositoryInterface;

/**
 * Hand-written in-memory double for TaskRepositoryInterface.
 *
 * Used by the Application layer tests to prove that use cases are testable
 * in complete isolation from Eloquent and the database.
 */
final class InMemoryTaskRepository implements TaskRepositoryInterface
{
    /** @var array<int, Task> */
    private array $tasks = [];

    private int $nextId = 1;

    /**
     * @return list<Task>
     */
    public function all(): array
    {
        return array_values($this->tasks);
    }

    public function find(int $id): ?Task
    {
        return $this->tasks[$id] ?? null;
    }

    public function save(Task $task): Task
    {
        if ($task->id() === null) {
            $persisted = Task::reconstitute(
                id: $this->nextId++,
                title: $task->title(),
                description: $task->description(),
                dueDate: $task->dueDate(),
                status: $task->status(),
                createdAt: $task->createdAt(),
                updatedAt: $task->updatedAt(),
            );

            $this->tasks[$persisted->id()] = $persisted;

            return $persisted;
        }

        $this->tasks[$task->id()] = $task;

        return $task;
    }

    public function delete(int $id): void
    {
        unset($this->tasks[$id]);
    }

    public function count(): int
    {
        return count($this->tasks);
    }
}
