<?php

declare(strict_types=1);

namespace App\Domain\Task\Entities;

use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Exceptions\InvalidTaskDataException;
use App\Domain\Task\Exceptions\TaskAlreadyCompletedException;
use DateTimeImmutable;

final class Task
{
    public const TITLE_MAX_LENGTH = 255;

    private function __construct(
        private ?int $id,
        private string $title,
        private ?string $description,
        private ?DateTimeImmutable $dueDate,
        private TaskStatus $status,
        private readonly DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
    ) {}

    public static function create(string $title, ?string $description, ?DateTimeImmutable $dueDate): self
    {
        self::guardTitle($title);
        self::guardDueDate($dueDate);

        $now = new DateTimeImmutable;

        return new self(
            id: null,
            title: $title,
            description: $description,
            dueDate: $dueDate,
            status: TaskStatus::Pending,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    /**
     * Rebuilds a Task from already-trusted persisted state, bypassing validation.
     */
    public static function reconstitute(
        int $id,
        string $title,
        ?string $description,
        ?DateTimeImmutable $dueDate,
        TaskStatus $status,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
    ): self {
        return new self($id, $title, $description, $dueDate, $status, $createdAt, $updatedAt);
    }

    public function update(string $title, ?string $description, ?DateTimeImmutable $dueDate): void
    {
        self::guardTitle($title);
        self::guardDueDate($dueDate);

        $this->title = $title;
        $this->description = $description;
        $this->dueDate = $dueDate;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function complete(): void
    {
        if ($this->status === TaskStatus::Completed) {
            throw TaskAlreadyCompletedException::withId($this->id ?? 0);
        }

        $this->status = TaskStatus::Completed;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function dueDate(): ?DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function status(): TaskStatus
    {
        return $this->status;
    }

    public function isCompleted(): bool
    {
        return $this->status === TaskStatus::Completed;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private static function guardTitle(string $title): void
    {
        $trimmed = trim($title);

        if ($trimmed === '') {
            throw InvalidTaskDataException::emptyTitle();
        }

        if (mb_strlen($trimmed) > self::TITLE_MAX_LENGTH) {
            throw InvalidTaskDataException::titleTooLong(self::TITLE_MAX_LENGTH);
        }
    }

    private static function guardDueDate(?DateTimeImmutable $dueDate): void
    {
        if ($dueDate === null) {
            return;
        }

        $today = new DateTimeImmutable('today');

        if ($dueDate < $today) {
            throw InvalidTaskDataException::dueDateInThePast();
        }
    }
}
