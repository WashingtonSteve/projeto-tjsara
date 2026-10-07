<?php

declare(strict_types=1);

namespace App\Application\Tag\UseCases;

use App\Domain\Tag\Entities\Tag;
use App\Domain\Tag\Repositories\TagRepositoryInterface;

final class ListTags
{
    public function __construct(
        private readonly TagRepositoryInterface $tags,
    ) {}

    /**
     * @return list<Tag>
     */
    public function __invoke(): array
    {
        return $this->tags->all();
    }
}
