<?php

declare(strict_types=1);

use App\Application\Task\DataTransferObjects\CreateTaskData;
use App\Application\Task\UseCases\CreateTask;
use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Exceptions\InvalidTaskDataException;
use Tests\Fakes\InMemoryTaskRepository;

test('it creates and persists a new pending task', function () {
    $repository = new InMemoryTaskRepository;
    $createTask = new CreateTask($repository);

    $task = $createTask(new CreateTaskData('Write the README', 'Make it shine', null));

    expect($task->id())->toBe(1)
        ->and($task->title())->toBe('Write the README')
        ->and($task->status())->toBe(TaskStatus::Pending)
        ->and($repository->count())->toBe(1);
});

test('it propagates domain validation failures without touching the repository', function () {
    $repository = new InMemoryTaskRepository;
    $createTask = new CreateTask($repository);

    expect(fn () => $createTask(new CreateTaskData('', null, null)))
        ->toThrow(InvalidTaskDataException::class);

    expect($repository->count())->toBe(0);
});
