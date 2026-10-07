<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Mappers;

use App\Domain\Tag\Entities\Tag;
use App\Infrastructure\Persistence\Eloquent\Models\EloquentTag;

final class TagMapper
{
    public static function toDomain(EloquentTag $model): Tag
    {
        return Tag::reconstitute(
            id: $model->id,
            name: $model->name,
        );
    }
}
