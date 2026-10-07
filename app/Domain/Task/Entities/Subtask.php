<?php

declare(strict_types=1);

namespace App\Domain\Task\Entities;

use App\Domain\Task\Exceptions\InvalidTaskDataException;

final class Subtask
{
    public const TITLE_MAX_LENGTH = 255;

    private function __construct(
        private ?int $id,
        private string $title,
        private bool $completed,
    ) {}

    public static function create(string $title): self
    {
        self::guardTitle($title);

        return new self(id: null, title: $title, completed: false);
    }

    /**
     * Rebuilds a Subtask from already-trusted persisted state, bypassing validation.
     */
    public static function reconstitute(int $id, string $title, bool $completed): self
    {
        return new self($id, $title, $completed);
    }

    public function complete(): void
    {
        $this->completed = true;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function isCompleted(): bool
    {
        return $this->completed;
    }

    private static function guardTitle(string $title): void
    {
        $trimmed = trim($title);

        if ($trimmed === '') {
            throw InvalidTaskDataException::emptySubtaskTitle();
        }

        if (mb_strlen($trimmed) > self::TITLE_MAX_LENGTH) {
            throw InvalidTaskDataException::subtaskTitleTooLong(self::TITLE_MAX_LENGTH);
        }
    }
}
