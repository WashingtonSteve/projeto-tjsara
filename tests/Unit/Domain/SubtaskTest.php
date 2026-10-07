<?php

declare(strict_types=1);

use App\Domain\Task\Entities\Subtask;
use App\Domain\Task\Exceptions\InvalidTaskDataException;

test('create builds a pending subtask', function () {
    $subtask = Subtask::create('Write the outline');

    expect($subtask->id())->toBeNull()
        ->and($subtask->title())->toBe('Write the outline')
        ->and($subtask->isCompleted())->toBeFalse();
});

test('create rejects an empty title', function (string $title) {
    Subtask::create($title);
})->with(['', '   '])->throws(InvalidTaskDataException::class, 'Subtask title must not be empty.');

test('create rejects a title longer than 255 characters', function () {
    Subtask::create(str_repeat('a', 256));
})->throws(InvalidTaskDataException::class, 'Subtask title must not exceed 255 characters.');

test('complete marks the subtask as completed', function () {
    $subtask = Subtask::create('Write the outline');

    $subtask->complete();

    expect($subtask->isCompleted())->toBeTrue();
});

test('reconstitute rebuilds a subtask from trusted persisted state', function () {
    $subtask = Subtask::reconstitute(id: 7, title: 'Historical subtask', completed: true);

    expect($subtask->id())->toBe(7)
        ->and($subtask->title())->toBe('Historical subtask')
        ->and($subtask->isCompleted())->toBeTrue();
});
