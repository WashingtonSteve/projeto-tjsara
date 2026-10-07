<?php

declare(strict_types=1);

namespace App\Domain\Task\Exceptions;

final class TaskAlreadyCompletedException extends \DomainException
{
    public static function withId(int $id): self
    {
        return new self("Task with ID [{$id}] is already completed.");
    }
}
