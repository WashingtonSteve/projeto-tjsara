<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Repositories;

use App\Domain\Tag\Entities\Tag;
use App\Domain\Tag\Repositories\TagRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Mappers\TagMapper;
use App\Infrastructure\Persistence\Eloquent\Models\EloquentTag;

final class EloquentTagRepository implements TagRepositoryInterface
{
    /**
     * @return list<Tag>
     */
    public function all(): array
    {
        return array_values(
            EloquentTag::query()
                ->orderBy('name')
                ->get()
                ->map(TagMapper::toDomain(...))
                ->all()
        );
    }

    public function find(int $id): ?Tag
    {
        $model = EloquentTag::query()->find($id);

        return $model ? TagMapper::toDomain($model) : null;
    }

    public function findByName(string $name): ?Tag
    {
        $model = EloquentTag::query()->where('name', $name)->first();

        return $model ? TagMapper::toDomain($model) : null;
    }

    public function save(Tag $tag): Tag
    {
        $model = $tag->id() !== null
            ? EloquentTag::query()->findOrFail($tag->id())
            : new EloquentTag;

        $model->fill(['name' => $tag->name()]);
        $model->save();

        return TagMapper::toDomain($model);
    }

    public function delete(int $id): void
    {
        EloquentTag::query()->whereKey($id)->delete();
    }
}
