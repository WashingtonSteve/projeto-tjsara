<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Mappers;

use App\Domain\Task\Entities\Task;
use App\Domain\Task\Enums\TaskStatus;
use App\Infrastructure\Persistence\Eloquent\Models\EloquentTask;
use DateTimeImmutable;

final class TaskMapper
{
    public static function toDomain(EloquentTask $model): Task
    {
        return Task::reconstitute(
            id: $model->id,
            title: $model->title,
            description: $model->description,
            dueDate: $model->due_date ? DateTimeImmutable::createFromInterface($model->due_date) : null,
            status: $model->status,
            createdAt: DateTimeImmutable::createFromInterface($model->created_at),
            updatedAt: DateTimeImmutable::createFromInterface($model->updated_at),
            subtasks: array_values($model->subtasks->map(SubtaskMapper::toDomain(...))->all()),
            tags: array_values($model->tags->map(TagMapper::toDomain(...))->all()),
        );
    }

    /**
     * @return array{title: string, description: string|null, due_date: string|null, status: TaskStatus}
     */
    public static function toAttributes(Task $task): array
    {
        return [
            'title' => $task->title(),
            'description' => $task->description(),
            'due_date' => $task->dueDate()?->format('Y-m-d'),
            'status' => $task->status(),
        ];
    }
}
