<?php

declare(strict_types=1);

namespace App\Domain\Tag\Exceptions;

final class TagNotFoundException extends \DomainException
{
    public static function withId(int $id): self
    {
        return new self("Tag with ID [{$id}] was not found.");
    }
}
