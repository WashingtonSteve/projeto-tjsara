<?php

declare(strict_types=1);

namespace App\Domain\Tag\Repositories;

use App\Domain\Tag\Entities\Tag;

interface TagRepositoryInterface
{
    /**
     * @return list<Tag>
     */
    public function all(): array;

    public function find(int $id): ?Tag;

    public function findByName(string $name): ?Tag;

    public function save(Tag $tag): Tag;

    public function delete(int $id): void;
}
