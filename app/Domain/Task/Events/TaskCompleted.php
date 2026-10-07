<?php

declare(strict_types=1);

namespace App\Domain\Task\Events;

use DateTimeImmutable;

/**
 * A pure domain event: plain data, no base class, no framework dependency.
 * The Application layer dispatches it through EventDispatcherInterface;
 * Infrastructure adapts that to Laravel's own event bus.
 */
final class TaskCompleted
{
    public function __construct(
        public readonly int $taskId,
        public readonly string $title,
        public readonly DateTimeImmutable $completedAt,
    ) {}
}
