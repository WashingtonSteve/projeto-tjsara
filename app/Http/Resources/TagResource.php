<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Domain\Tag\Entities\Tag;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Tag */
final class TagResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Tag $tag */
        $tag = $this->resource;

        return [
            'id' => $tag->id(),
            'name' => $tag->name(),
        ];
    }
}
