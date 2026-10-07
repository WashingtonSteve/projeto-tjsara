<?php

declare(strict_types=1);

use App\Domain\Tag\Entities\Tag;
use App\Domain\Task\Entities\Subtask;
use App\Domain\Task\Entities\Task;
use App\Domain\Task\Enums\TaskStatus;
use App\Domain\Task\Exceptions\InvalidTaskDataException;
use App\Domain\Task\Exceptions\SubtaskNotFoundException;
use App\Domain\Task\Exceptions\TagNotAttachedException;
use App\Domain\Task\Exceptions\TaskAlreadyCompletedException;
use App\Domain\Task\Exceptions\TaskHasPendingSubtasksException;

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

test('addSubtask appends a pending subtask and returns it', function () {
    $task = Task::create('Plan the trip', null, null);

    $subtask = $task->addSubtask('Book flights');

    expect($subtask->title())->toBe('Book flights')
        ->and($subtask->isCompleted())->toBeFalse()
        ->and($task->subtasks())->toHaveCount(1);
});

test('completeSubtask marks the matching subtask as completed', function () {
    $task = Task::reconstitute(
        id: 1,
        title: 'Plan the trip',
        description: null,
        dueDate: null,
        status: TaskStatus::Pending,
        createdAt: new DateTimeImmutable,
        updatedAt: new DateTimeImmutable,
        subtasks: [Subtask::reconstitute(1, 'Book flights', false)],
    );

    $task->completeSubtask(1);

    expect($task->subtasks()[0]->isCompleted())->toBeTrue();
});

test('completeSubtask throws SubtaskNotFoundException for an unknown id', function () {
    $task = Task::create('Plan the trip', null, null);

    $task->completeSubtask(999);
})->throws(SubtaskNotFoundException::class, 'Subtask with ID [999] was not found.');

test('removeSubtask drops the matching subtask', function () {
    $task = Task::reconstitute(
        id: 1,
        title: 'Plan the trip',
        description: null,
        dueDate: null,
        status: TaskStatus::Pending,
        createdAt: new DateTimeImmutable,
        updatedAt: new DateTimeImmutable,
        subtasks: [Subtask::reconstitute(1, 'Book flights', false)],
    );

    $task->removeSubtask(1);

    expect($task->subtasks())->toBeEmpty();
});

test('removeSubtask throws SubtaskNotFoundException for an unknown id', function () {
    $task = Task::create('Plan the trip', null, null);

    $task->removeSubtask(999);
})->throws(SubtaskNotFoundException::class);

test('complete throws TaskHasPendingSubtasksException when a subtask is still pending', function () {
    $task = Task::reconstitute(
        id: 1,
        title: 'Plan the trip',
        description: null,
        dueDate: null,
        status: TaskStatus::Pending,
        createdAt: new DateTimeImmutable,
        updatedAt: new DateTimeImmutable,
        subtasks: [Subtask::reconstitute(1, 'Book flights', false)],
    );

    $task->complete();
})->throws(TaskHasPendingSubtasksException::class, 'Task with ID [1] has pending subtasks and cannot be completed.');

test('complete succeeds when every subtask is already completed', function () {
    $task = Task::reconstitute(
        id: 1,
        title: 'Plan the trip',
        description: null,
        dueDate: null,
        status: TaskStatus::Pending,
        createdAt: new DateTimeImmutable,
        updatedAt: new DateTimeImmutable,
        subtasks: [Subtask::reconstitute(1, 'Book flights', true)],
    );

    $task->complete();

    expect($task->isCompleted())->toBeTrue();
});

test('attachTag adds a tag to the task', function () {
    $task = Task::create('Plan the trip', null, null);
    $tag = Tag::reconstitute(1, 'urgent');

    $task->attachTag($tag);

    expect($task->tags())->toHaveCount(1)
        ->and($task->hasTag(1))->toBeTrue();
});

test('attachTag is idempotent for an already-attached tag', function () {
    $task = Task::create('Plan the trip', null, null);
    $tag = Tag::reconstitute(1, 'urgent');

    $task->attachTag($tag);
    $task->attachTag($tag);

    expect($task->tags())->toHaveCount(1);
});

test('detachTag removes a previously attached tag', function () {
    $task = Task::create('Plan the trip', null, null);
    $task->attachTag(Tag::reconstitute(1, 'urgent'));

    $task->detachTag(1);

    expect($task->tags())->toBeEmpty()
        ->and($task->hasTag(1))->toBeFalse();
});

test('detachTag throws TagNotAttachedException when the tag is not attached', function () {
    $task = Task::create('Plan the trip', null, null);

    $task->detachTag(999);
})->throws(TagNotAttachedException::class, 'Tag with ID [999] is not attached to this task.');

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
