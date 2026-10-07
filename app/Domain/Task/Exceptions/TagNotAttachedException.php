<?php

declare(strict_types=1);

namespace App\Domain\Task\Exceptions;

final class TagNotAttachedException extends \DomainException
{
    public static function withId(int $tagId): self
    {
        return new self("Tag with ID [{$tagId}] is not attached to this task.");
    }
}
