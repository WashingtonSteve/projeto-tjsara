<?php

declare(strict_types=1);

namespace App\Domain\Task\Entities;

use App\Domain\Tag\Entities\Tag;
use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Exceptions\InvalidTaskDataException;
use App\Domain\Task\Exceptions\SubtaskNotFoundException;
use App\Domain\Task\Exceptions\TagNotAttachedException;
use App\Domain\Task\Exceptions\TaskAlreadyCompletedException;
use App\Domain\Task\Exceptions\TaskHasPendingSubtasksException;
use DateTimeImmutable;

final class Task
{
    public const TITLE_MAX_LENGTH = 255;

    /** @var list<Subtask> */
    private array $subtasks;

    /** @var list<Tag> */
    private array $tags;

    /**
     * @param  list<Subtask>  $subtasks
     * @param  list<Tag>  $tags
     */
    private function __construct(
        private ?int $id,
        private string $title,
        private ?string $description,
        private ?DateTimeImmutable $dueDate,
        private TaskStatus $status,
        private readonly DateTimeImmutable $createdAt,
        private DateTimeImmutable $updatedAt,
        array $subtasks = [],
        array $tags = [],
    ) {
        $this->subtasks = $subtasks;
        $this->tags = $tags;
    }

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
     *
     * @param  list<Subtask>  $subtasks
     * @param  list<Tag>  $tags
     */
    public static function reconstitute(
        int $id,
        string $title,
        ?string $description,
        ?DateTimeImmutable $dueDate,
        TaskStatus $status,
        DateTimeImmutable $createdAt,
        DateTimeImmutable $updatedAt,
        array $subtasks = [],
        array $tags = [],
    ): self {
        return new self($id, $title, $description, $dueDate, $status, $createdAt, $updatedAt, $subtasks, $tags);
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

        if ($this->hasPendingSubtasks()) {
            throw TaskHasPendingSubtasksException::withId($this->id ?? 0);
        }

        $this->status = TaskStatus::Completed;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function addSubtask(string $title): Subtask
    {
        $subtask = Subtask::create($title);

        $this->subtasks[] = $subtask;
        $this->updatedAt = new DateTimeImmutable;

        return $subtask;
    }

    public function completeSubtask(int $subtaskId): void
    {
        $this->findSubtask($subtaskId)->complete();
        $this->updatedAt = new DateTimeImmutable;
    }

    public function removeSubtask(int $subtaskId): void
    {
        $this->findSubtask($subtaskId);

        $this->subtasks = array_values(array_filter(
            $this->subtasks,
            fn (Subtask $subtask): bool => $subtask->id() !== $subtaskId,
        ));

        $this->updatedAt = new DateTimeImmutable;
    }

    public function hasPendingSubtasks(): bool
    {
        foreach ($this->subtasks as $subtask) {
            if (! $subtask->isCompleted()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Attaching an already-attached tag is a no-op, not an error.
     */
    public function attachTag(Tag $tag): void
    {
        if ($this->hasTag($tag->id())) {
            return;
        }

        $this->tags[] = $tag;
        $this->updatedAt = new DateTimeImmutable;
    }

    public function detachTag(int $tagId): void
    {
        if (! $this->hasTag($tagId)) {
            throw TagNotAttachedException::withId($tagId);
        }

        $this->tags = array_values(array_filter(
            $this->tags,
            fn (Tag $tag): bool => $tag->id() !== $tagId,
        ));

        $this->updatedAt = new DateTimeImmutable;
    }

    public function hasTag(?int $tagId): bool
    {
        foreach ($this->tags as $tag) {
            if ($tag->id() === $tagId) {
                return true;
            }
        }

        return false;
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

    /**
     * @return list<Subtask>
     */
    public function subtasks(): array
    {
        return $this->subtasks;
    }

    /**
     * @return list<Tag>
     */
    public function tags(): array
    {
        return $this->tags;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private function findSubtask(int $subtaskId): Subtask
    {
        foreach ($this->subtasks as $subtask) {
            if ($subtask->id() === $subtaskId) {
                return $subtask;
            }
        }

        throw SubtaskNotFoundException::withId($subtaskId);
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
