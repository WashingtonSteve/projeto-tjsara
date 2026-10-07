<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Task\Entities\Subtask;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Subtask */
final class SubtaskResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Subtask $subtask */
        $subtask = $this->resource;

        return [
            'id' => $subtask->id(),
            'title' => $subtask->title(),
            'completed' => $subtask->isCompleted(),
        ];
    }
}
