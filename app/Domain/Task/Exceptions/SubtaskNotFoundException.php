<?php

declare(strict_types=1);

namespace App\Domain\Task\Exceptions;

final class SubtaskNotFoundException extends \DomainException
{
    public static function withId(int $id): self
    {
        return new self("Subtask with ID [{$id}] was not found.");
    }
}
