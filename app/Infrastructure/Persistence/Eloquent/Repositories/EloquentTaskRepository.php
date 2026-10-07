<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Tag\Entities\Tag;
use App\Domain\Task\Entities\Subtask;
use App\Domain\Task\Entities\Task;
use App\Domain\Task\Repositories\TaskRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Mappers\TaskMapper;
use App\Infrastructure\Persistence\Eloquent\Models\EloquentTask;

final class EloquentTaskRepository implements TaskRepositoryInterface
{
    private const WITH = ['subtasks', 'tags'];

    /**
     * @return list<Task>
     */
    public function all(): array
    {
        return array_values(
            EloquentTask::query()
                ->with(self::WITH)
                ->orderBy('id')
                ->get()
                ->map(TaskMapper::toDomain(...))
                ->all()
        );
    }

    public function find(int $id): ?Task
    {
        $model = EloquentTask::query()->with(self::WITH)->find($id);

        return $model ? TaskMapper::toDomain($model) : null;
    }

    public function save(Task $task): Task
    {
        $model = $task->id() !== null
            ? EloquentTask::query()->with(self::WITH)->findOrFail($task->id())
            : new EloquentTask;

        $model->fill(TaskMapper::toAttributes($task));
        $model->save();

        $this->syncSubtasks($model, $task->subtasks());
        $this->syncTags($model, $task->tags());

        return TaskMapper::toDomain($model->fresh(self::WITH) ?? $model);
    }

    public function delete(int $id): void
    {
        EloquentTask::query()->whereKey($id)->delete();
    }

    /**
     * @param  list<Subtask>  $subtasks
     */
    private function syncSubtasks(EloquentTask $model, array $subtasks): void
    {
        $keptIds = [];

        foreach ($subtasks as $subtask) {
            if ($subtask->id() !== null) {
                $keptIds[] = $subtask->id();

                $model->subtasks()->whereKey($subtask->id())->update([
                    'title' => $subtask->title(),
                    'completed' => $subtask->isCompleted(),
                ]);

                continue;
            }

            $created = $model->subtasks()->create([
                'title' => $subtask->title(),
                'completed' => $subtask->isCompleted(),
            ]);

            $keptIds[] = $created->id;
        }

        $model->subtasks()->whereNotIn('id', $keptIds)->delete();
    }

    /**
     * @param  list<Tag>  $tags
     */
    private function syncTags(EloquentTask $model, array $tags): void
    {
        $tagIds = [];

        foreach ($tags as $tag) {
            if ($tag->id() !== null) {
                $tagIds[] = $tag->id();
            }
        }

        $model->tags()->sync($tagIds);
    }
}
