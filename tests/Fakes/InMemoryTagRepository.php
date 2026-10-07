<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\Domain\Tag\Entities\Tag;
use App\Domain\Tag\Repositories\TagRepositoryInterface;

/**
 * Hand-written in-memory double for TagRepositoryInterface.
 */
final class InMemoryTagRepository implements TagRepositoryInterface
{
    /** @var array<int, Tag> */
    private array $tags = [];

    private int $nextId = 1;

    /**
     * @return list<Tag>
     */
    public function all(): array
    {
        return array_values($this->tags);
    }

    public function find(int $id): ?Tag
    {
        return $this->tags[$id] ?? null;
    }

    public function findByName(string $name): ?Tag
    {
        foreach ($this->tags as $tag) {
            if ($tag->name() === $name) {
                return $tag;
            }
        }

        return null;
    }

    public function save(Tag $tag): Tag
    {
        $id = $tag->id() ?? $this->nextId++;

        $persisted = Tag::reconstitute($id, $tag->name());

        $this->tags[$id] = $persisted;

        return $persisted;
    }

    public function delete(int $id): void
    {
        unset($this->tags[$id]);
    }

    public function count(): int
    {
        return count($this->tags);
    }
}
