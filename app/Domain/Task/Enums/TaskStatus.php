<?php

declare(strict_types=1);

namespace App\Domain\Task\Enums;

enum TaskStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
}
