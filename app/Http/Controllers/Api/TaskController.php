<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\Task\DataTransferObjects\CreateTaskData;
use App\Application\Task\DataTransferObjects\UpdateTaskData;
use App\Application\Task\UseCases\CompleteTask;
use App\Application\Task\UseCases\CreateTask;
use App\Application\Task\UseCases\DeleteTask;
use App\Application\Task\UseCases\FindTask;
use App\Application\Task\UseCases\ListTasks;
use App\Application\Task\UseCases\UpdateTask;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

final class TaskController extends Controller
{
    public function __construct(
        private readonly ListTasks $listTasks,
        private readonly CreateTask $createTask,
        private readonly FindTask $findTask,
        private readonly UpdateTask $updateTask,
        private readonly CompleteTask $completeTask,
        private readonly DeleteTask $deleteTask,
    ) {}

    public function index(): AnonymousResourceCollection
    {
        return TaskResource::collection(($this->listTasks)());
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = ($this->createTask)(CreateTaskData::fromArray($request->validated()));

        return TaskResource::make($task)
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    public function show(int $task): TaskResource
    {
        return TaskResource::make(($this->findTask)($task));
    }

    public function update(UpdateTaskRequest $request, int $task): TaskResource
    {
        $data = UpdateTaskData::fromArray($request->validated());

        return TaskResource::make(($this->updateTask)($task, $data));
    }

    public function complete(int $task): TaskResource
    {
        return TaskResource::make(($this->completeTask)($task));
    }

    public function destroy(int $task): Response
    {
        ($this->deleteTask)($task);

        return response()->noContent();
    }
}
