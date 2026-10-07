<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ActivityLog;
use DateTimeInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ActivityLog */
final class ActivityLogResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'description' => $this->description,
            'created_at' => $this->created_at->format(DateTimeInterface::ATOM),
        ];
    }
}
