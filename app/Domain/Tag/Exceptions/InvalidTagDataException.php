<?php

declare(strict_types=1);

namespace App\Domain\Tag\Exceptions;

final class InvalidTagDataException extends \DomainException
{
    public static function emptyName(): self
    {
        return new self('Tag name must not be empty.');
    }

    public static function nameTooLong(int $max): self
    {
        return new self("Tag name must not exceed {$max} characters.");
    }
}
