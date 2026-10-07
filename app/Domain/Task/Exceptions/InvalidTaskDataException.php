<?php

declare(strict_types=1);

namespace App\Domain\Task\Exceptions;

final class InvalidTaskDataException extends \DomainException
{
    public static function emptyTitle(): self
    {
        return new self('Task title must not be empty.');
    }

    public static function titleTooLong(int $max): self
    {
        return new self("Task title must not exceed {$max} characters.");
    }

    public static function dueDateInThePast(): self
    {
        return new self('Task due date must not be in the past.');
    }
}
