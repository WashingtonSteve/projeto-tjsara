<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Mappers;

use App\Domain\Task\Entities\Subtask;
use App\Infrastructure\Persistence\Eloquent\Models\EloquentSubtask;

final class SubtaskMapper
{
    public static function toDomain(EloquentSubtask $model): Subtask
    {
        return Subtask::reconstitute(
            id: $model->id,
            title: $model->title,
            completed: $model->completed,
        );
    }
}
