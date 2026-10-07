<?php

declare(strict_types=1);

namespace App\Application\Task\DataTransferObjects;

use DateTimeImmutable;

final readonly class UpdateTaskData
{
    public function __construct(
        public string $title,
        public ?string $description,
        public ?DateTimeImmutable $dueDate,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            title: $data['title'],
            description: $data['description'] ?? null,
            dueDate: isset($data['due_date']) ? new DateTimeImmutable($data['due_date']) : null,
        );
    }
}
