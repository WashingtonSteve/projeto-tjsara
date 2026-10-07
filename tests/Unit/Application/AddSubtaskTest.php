<?php

declare(strict_types=1);

use App\Application\Task\DataTransferObjects\CreateTaskData;
use App\Application\Task\UseCases\AddSubtask;
use App\Application\Task\UseCases\CreateTask;
use App\Domain\Task\Exceptions\InvalidTaskDataException;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use Tests\Fakes\InMemoryTaskRepository;

test('it adds a subtask to an existing task', function () {
    $repository = new InMemoryTaskRepository;
    $task = (new CreateTask($repository))(new CreateTaskData('Plan the trip', null, null));

    $updated = (new AddSubtask($repository))($task->id(), 'Book flights');

    expect($updated->subtasks())->toHaveCount(1)
        ->and($updated->subtasks()[0]->title())->toBe('Book flights')
        ->and($updated->subtasks()[0]->id())->not->toBeNull();
});

test('it throws TaskNotFoundException when the task does not exist', function () {
    $addSubtask = new AddSubtask(new InMemoryTaskRepository);

    $addSubtask(999, 'Book flights');
})->throws(TaskNotFoundException::class);

test('it propagates domain validation failures for an empty subtask title', function () {
    $repository = new InMemoryTaskRepository;
    $task = (new CreateTask($repository))(new CreateTaskData('Plan the trip', null, null));

    $addSubtask = new AddSubtask($repository);

    $addSubtask($task->id(), '');
})->throws(InvalidTaskDataException::class);
