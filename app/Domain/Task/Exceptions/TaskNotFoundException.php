<?php

declare(strict_types=1);

namespace App\Domain\Task\Exceptions;

final class TaskNotFoundException extends \DomainException
{
    public static function withId(int $id): self
    {
        return new self("Task with ID [{$id}] was not found.");
    }
}
