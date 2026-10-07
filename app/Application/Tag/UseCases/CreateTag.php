<?php

declare(strict_types=1);

namespace App\Application\Tag\UseCases;

use App\Domain\Tag\Entities\Tag;
use App\Domain\Tag\Repositories\TagRepositoryInterface;

final class CreateTag
{
    public function __construct(
        private readonly TagRepositoryInterface $tags,
    ) {}

    public function __invoke(string $name): Tag
    {
        return $this->tags->save(Tag::create($name));
    }
}
