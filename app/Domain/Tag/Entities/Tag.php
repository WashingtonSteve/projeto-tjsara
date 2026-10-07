<?php

declare(strict_types=1);

namespace App\Domain\Tag\Entities;

use App\Domain\Tag\Exceptions\InvalidTagDataException;

final class Tag
{
    public const NAME_MAX_LENGTH = 50;

    private function __construct(
        private ?int $id,
        private string $name,
    ) {}

    public static function create(string $name): self
    {
        self::guardName($name);

        return new self(id: null, name: trim($name));
    }

    /**
     * Rebuilds a Tag from already-trusted persisted state, bypassing validation.
     */
    public static function reconstitute(int $id, string $name): self
    {
        return new self($id, $name);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    private static function guardName(string $name): void
    {
        $trimmed = trim($name);

        if ($trimmed === '') {
            throw InvalidTagDataException::emptyName();
        }

        if (mb_strlen($trimmed) > self::NAME_MAX_LENGTH) {
            throw InvalidTagDataException::nameTooLong(self::NAME_MAX_LENGTH);
        }
    }
}
