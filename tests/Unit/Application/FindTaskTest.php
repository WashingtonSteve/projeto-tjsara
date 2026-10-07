<?php

declare(strict_types=1);

use App\Application\Task\DataTransferObjects\CreateTaskData;
use App\Application\Task\UseCases\CreateTask;
use App\Application\Task\UseCases\FindTask;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use Tests\Fakes\InMemoryTaskRepository;

test('it finds an existing task by id', function () {
    $repository = new InMemoryTaskRepository;
    $created = (new CreateTask($repository))(new CreateTaskData('Find me', null, null));

    $task = (new FindTask($repository))($created->id());

    expect($task->title())->toBe('Find me');
});

test('it throws TaskNotFoundException when the task does not exist', function () {
    $findTask = new FindTask(new InMemoryTaskRepository);

    $findTask(999);
})->throws(TaskNotFoundException::class);
