<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Task\Entities\Task;
use App\Domain\Task\Repositories\TaskRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Mappers\TaskMapper;
use App\Infrastructure\Persistence\Eloquent\Models\EloquentTask;

final class EloquentTaskRepository implements TaskRepositoryInterface
{
    /**
     * @return list<Task>
     */
    public function all(): array
    {
        return array_values(
            EloquentTask::query()
                ->orderBy('id')
                ->get()
                ->map(TaskMapper::toDomain(...))
                ->all()
        );
    }

    public function find(int $id): ?Task
    {
        $model = EloquentTask::query()->find($id);

        return $model ? TaskMapper::toDomain($model) : null;
    }

    public function save(Task $task): Task
    {
        $model = $task->id() !== null
            ? EloquentTask::query()->findOrFail($task->id())
            : new EloquentTask;

        $model->fill(TaskMapper::toAttributes($task));
        $model->save();

        return TaskMapper::toDomain($model);
    }

    public function delete(int $id): void
    {
        EloquentTask::query()->whereKey($id)->delete();
    }
}
