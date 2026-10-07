<?php

declare(strict_types=1);

namespace App\Domain\Task\Exceptions;

final class TaskHasPendingSubtasksException extends \DomainException
{
    public static function withId(int $id): self
    {
        return new self("Task with ID [{$id}] has pending subtasks and cannot be completed.");
    }
}
