<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Task\UseCases\AttachTag;
use App\Application\Task\UseCases\DetachTag;
use App\Http\Controllers\Controller;
use App\Http\Requests\AttachTagRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class TaskTagController extends Controller
{
    public function __construct(
        private readonly AttachTag $attachTag,
        private readonly DetachTag $detachTag,
    ) {}

    public function store(AttachTagRequest $request, int $task): JsonResponse
    {
        $updated = ($this->attachTag)($task, $request->string('name')->toString());

        return TaskResource::make($updated)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function destroy(int $task, int $tag): TaskResource
    {
        return TaskResource::make(($this->detachTag)($task, $tag));
    }
}
