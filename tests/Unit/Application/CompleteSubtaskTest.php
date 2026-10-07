<?php

declare(strict_types=1);

use App\Application\Task\DataTransferObjects\CreateTaskData;
use App\Application\Task\UseCases\AddSubtask;
use App\Application\Task\UseCases\CompleteSubtask;
use App\Application\Task\UseCases\CreateTask;
use App\Domain\Task\Exceptions\SubtaskNotFoundException;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use Tests\Fakes\InMemoryTaskRepository;

test('it marks a subtask as completed', function () {
    $repository = new InMemoryTaskRepository;
    $task = (new CreateTask($repository))(new CreateTaskData('Plan the trip', null, null));
    $withSubtask = (new AddSubtask($repository))($task->id(), 'Book flights');
    $subtaskId = $withSubtask->subtasks()[0]->id();

    $updated = (new CompleteSubtask($repository))($task->id(), $subtaskId);

    expect($updated->subtasks()[0]->isCompleted())->toBeTrue();
});

test('it throws TaskNotFoundException when the task does not exist', function () {
    $completeSubtask = new CompleteSubtask(new InMemoryTaskRepository);

    $completeSubtask(999, 1);
})->throws(TaskNotFoundException::class);

test('it throws SubtaskNotFoundException when the subtask does not exist', function () {
    $repository = new InMemoryTaskRepository;
    $task = (new CreateTask($repository))(new CreateTaskData('Plan the trip', null, null));

    $completeSubtask = new CompleteSubtask($repository);

    $completeSubtask($task->id(), 999);
})->throws(SubtaskNotFoundException::class);
