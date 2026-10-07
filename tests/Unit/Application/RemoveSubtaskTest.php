<?php

declare(strict_types=1);

use App\Application\Task\DataTransferObjects\CreateTaskData;
use App\Application\Task\UseCases\AddSubtask;
use App\Application\Task\UseCases\CreateTask;
use App\Application\Task\UseCases\RemoveSubtask;
use App\Domain\Task\Exceptions\SubtaskNotFoundException;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use Tests\Fakes\InMemoryTaskRepository;

test('it removes a subtask', function () {
    $repository = new InMemoryTaskRepository;
    $task = (new CreateTask($repository))(new CreateTaskData('Plan the trip', null, null));
    $withSubtask = (new AddSubtask($repository))($task->id(), 'Book flights');
    $subtaskId = $withSubtask->subtasks()[0]->id();

    $updated = (new RemoveSubtask($repository))($task->id(), $subtaskId);

    expect($updated->subtasks())->toBeEmpty();
});

test('it throws TaskNotFoundException when the task does not exist', function () {
    $removeSubtask = new RemoveSubtask(new InMemoryTaskRepository);

    $removeSubtask(999, 1);
})->throws(TaskNotFoundException::class);

test('it throws SubtaskNotFoundException when the subtask does not exist', function () {
    $repository = new InMemoryTaskRepository;
    $task = (new CreateTask($repository))(new CreateTaskData('Plan the trip', null, null));

    $removeSubtask = new RemoveSubtask($repository);

    $removeSubtask($task->id(), 999);
})->throws(SubtaskNotFoundException::class);
