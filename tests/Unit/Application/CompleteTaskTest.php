<?php

declare(strict_types=1);

use App\Application\Task\DataTransferObjects\CreateTaskData;
use App\Application\Task\UseCases\CompleteTask;
use App\Application\Task\UseCases\CreateTask;
use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Events\TaskCompleted;
use App\Domain\Task\Exceptions\TaskAlreadyCompletedException;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use Tests\Fakes\InMemoryEventDispatcher;
use Tests\Fakes\InMemoryTaskRepository;

test('it marks a pending task as completed', function () {
    $repository = new InMemoryTaskRepository;
    $created = (new CreateTask($repository))(new CreateTaskData('Finish the chapter', null, null));

    $completed = (new CompleteTask($repository, new InMemoryEventDispatcher))($created->id());

    expect($completed->status())->toBe(TaskStatus::Completed);
});

test('it dispatches a TaskCompleted event', function () {
    $repository = new InMemoryTaskRepository;
    $events = new InMemoryEventDispatcher;
    $created = (new CreateTask($repository))(new CreateTaskData('Finish the chapter', null, null));

    (new CompleteTask($repository, $events))($created->id());

    expect($events->dispatched())->toHaveCount(1);

    $event = $events->dispatched()[0];
    expect($event)->toBeInstanceOf(TaskCompleted::class)
        ->and($event->taskId)->toBe($created->id())
        ->and($event->title)->toBe('Finish the chapter');
});

test('it throws TaskAlreadyCompletedException when completing twice', function () {
    $repository = new InMemoryTaskRepository;
    $created = (new CreateTask($repository))(new CreateTaskData('Finish the chapter', null, null));

    $completeTask = new CompleteTask($repository, new InMemoryEventDispatcher);
    $completeTask($created->id());

    $completeTask($created->id());
})->throws(TaskAlreadyCompletedException::class);

test('it throws TaskNotFoundException when completing a missing task', function () {
    $completeTask = new CompleteTask(new InMemoryTaskRepository, new InMemoryEventDispatcher);

    $completeTask(999);
})->throws(TaskNotFoundException::class);
