<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Task\UseCases\AddSubtask;
use App\Application\Task\UseCases\CompleteSubtask;
use App\Application\Task\UseCases\RemoveSubtask;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSubtaskRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

final class SubtaskController extends Controller
{
    public function __construct(
        private readonly AddSubtask $addSubtask,
        private readonly CompleteSubtask $completeSubtask,
        private readonly RemoveSubtask $removeSubtask,
    ) {}

    public function store(StoreSubtaskRequest $request, int $task): JsonResponse
    {
        $updated = ($this->addSubtask)($task, $request->string('title')->toString());

        return TaskResource::make($updated)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function complete(int $task, int $subtask): TaskResource
    {
        return TaskResource::make(($this->completeSubtask)($task, $subtask));
    }

    public function destroy(int $task, int $subtask): TaskResource
    {
        return TaskResource::make(($this->removeSubtask)($task, $subtask));
    }
}
