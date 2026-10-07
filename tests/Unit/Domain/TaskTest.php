<?php

declare(strict_types=1);

use App\Domain\Task\Entities\Task;
use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Exceptions\InvalidTaskDataException;
use App\Domain\Task\Exceptions\TaskAlreadyCompletedException;

test('create builds a pending task with the given data', function () {
    $dueDate = new DateTimeImmutable('+1 week');

    $task = Task::create('Write the architecture docs', 'Cover all four layers', $dueDate);

    expect($task->id())->toBeNull()
        ->and($task->title())->toBe('Write the architecture docs')
        ->and($task->description())->toBe('Cover all four layers')
        ->and($task->dueDate())->toEqual($dueDate)
        ->and($task->status())->toBe(TaskStatus::Pending)
        ->and($task->isCompleted())->toBeFalse();
});

test('create allows a null description and a null due date', function () {
    $task = Task::create('Ship the release', null, null);

    expect($task->description())->toBeNull()
        ->and($task->dueDate())->toBeNull();
});

test('create rejects an empty title', function (string $title) {
    Task::create($title, null, null);
})->with(['', '   '])->throws(InvalidTaskDataException::class, 'Task title must not be empty.');

test('create rejects a title longer than 255 characters', function () {
    Task::create(str_repeat('a', 256), null, null);
})->throws(InvalidTaskDataException::class, 'Task title must not exceed 255 characters.');

test('create accepts a title of exactly 255 characters', function () {
    $task = Task::create(str_repeat('a', 255), null, null);

    expect(mb_strlen($task->title()))->toBe(255);
});

test('create rejects a due date in the past', function () {
    Task::create('Backdated task', null, new DateTimeImmutable('-1 day'));
})->throws(InvalidTaskDataException::class, 'Task due date must not be in the past.');

test('create accepts a due date of today', function () {
    $task = Task::create('Due today', null, new DateTimeImmutable('today'));

    expect($task->dueDate())->toEqual(new DateTimeImmutable('today'));
});

test('update revalidates the title and the due date', function () {
    $task = Task::create('Original title', null, null);

    $task->update('Updated title', 'Updated description', new DateTimeImmutable('+1 day'));

    expect($task->title())->toBe('Updated title')
        ->and($task->description())->toBe('Updated description');
});

test('update rejects an empty title', function () {
    $task = Task::create('Original title', null, null);

    $task->update('', null, null);
})->throws(InvalidTaskDataException::class, 'Task title must not be empty.');

test('update rejects a due date in the past', function () {
    $task = Task::create('Original title', null, null);

    $task->update('Original title', null, new DateTimeImmutable('-1 day'));
})->throws(InvalidTaskDataException::class, 'Task due date must not be in the past.');

test('complete marks a pending task as completed', function () {
    $task = Task::create('Finish the report', null, null);

    $task->complete();

    expect($task->status())->toBe(TaskStatus::Completed)
        ->and($task->isCompleted())->toBeTrue();
});

test('complete throws when the task is already completed', function () {
    $task = Task::create('Finish the report', null, null);
    $task->complete();

    $task->complete();
})->throws(TaskAlreadyCompletedException::class);

test('reconstitute rebuilds a task from trusted persisted state without revalidating', function () {
    $dueDate = new DateTimeImmutable('-10 days');

    $task = Task::reconstitute(
        id: 42,
        title: 'Historical task',
        description: null,
        dueDate: $dueDate,
        status: TaskStatus::Completed,
        createdAt: new DateTimeImmutable('-20 days'),
        updatedAt: new DateTimeImmutable('-15 days'),
    );

    expect($task->id())->toBe(42)
        ->and($task->dueDate())->toBe($dueDate)
        ->and($task->isCompleted())->toBeTrue();
});
