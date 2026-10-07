<?php

declare(strict_types=1);

use App\Application\Task\DataTransferObjects\CreateTaskData;
use App\Application\Task\UseCases\CreateTask;
use App\Application\Task\UseCases\DeleteTask;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use Tests\Fakes\InMemoryTaskRepository;

test('it deletes an existing task', function () {
    $repository = new InMemoryTaskRepository;
    $created = (new CreateTask($repository))(new CreateTaskData('Disposable task', null, null));

    (new DeleteTask($repository))($created->id());

    expect($repository->count())->toBe(0);
});

test('it throws TaskNotFoundException when deleting a missing task', function () {
    $deleteTask = new DeleteTask(new InMemoryTaskRepository);

    $deleteTask(999);
})->throws(TaskNotFoundException::class);
