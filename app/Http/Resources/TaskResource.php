<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Task\Entities\Task;
use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Task */
final class TaskResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Task $task */
        $task = $this->resource;

        return [
            'id' => $task->id(),
            'title' => $task->title(),
            'description' => $task->description(),
            'due_date' => $task->dueDate()?->format('Y-m-d'),
            'status' => $task->status()->value,
            'created_at' => $task->createdAt()->format(DateTimeInterface::ATOM),
            'updated_at' => $task->updatedAt()->format(DateTimeInterface::ATOM),
        ];
    }
}
