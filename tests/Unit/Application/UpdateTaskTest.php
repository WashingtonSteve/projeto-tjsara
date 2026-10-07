<?php

declare(strict_types=1);

use App\Application\Task\DataTransferObjects\CreateTaskData;
use App\Application\Task\DataTransferObjects\UpdateTaskData;
use App\Application\Task\UseCases\CreateTask;
use App\Application\Task\UseCases\UpdateTask;
use App\Domain\Task\Exceptions\InvalidTaskDataException;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use Tests\Fakes\InMemoryTaskRepository;

test('it updates an existing task', function () {
    $repository = new InMemoryTaskRepository;
    $created = (new CreateTask($repository))(new CreateTaskData('Original', 'Original description', null));

    $updated = (new UpdateTask($repository))($created->id(), new UpdateTaskData('Updated', 'Updated description', null));

    expect($updated->id())->toBe($created->id())
        ->and($updated->title())->toBe('Updated')
        ->and($updated->description())->toBe('Updated description');
});

test('it throws TaskNotFoundException when updating a missing task', function () {
    $updateTask = new UpdateTask(new InMemoryTaskRepository);

    $updateTask(999, new UpdateTaskData('Anything', null, null));
})->throws(TaskNotFoundException::class);

test('it propagates domain validation failures', function () {
    $repository = new InMemoryTaskRepository;
    $created = (new CreateTask($repository))(new CreateTaskData('Original', null, null));

    $updateTask = new UpdateTask($repository);

    $updateTask($created->id(), new UpdateTaskData('', null, null));
})->throws(InvalidTaskDataException::class);
