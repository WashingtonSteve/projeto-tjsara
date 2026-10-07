<?php

declare(strict_types=1);

use App\Application\Tag\UseCases\CreateTag;
use App\Application\Task\DataTransferObjects\CreateTaskData;
use App\Application\Task\UseCases\AttachTag;
use App\Application\Task\UseCases\CreateTask;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use Tests\Fakes\InMemoryTagRepository;
use Tests\Fakes\InMemoryTaskRepository;

test('it attaches an existing tag found by name', function () {
    $tasks = new InMemoryTaskRepository;
    $tags = new InMemoryTagRepository;
    $existing = (new CreateTag($tags))('urgent');
    $task = (new CreateTask($tasks))(new CreateTaskData('Plan the trip', null, null));

    $updated = (new AttachTag($tasks, $tags))($task->id(), 'urgent');

    expect($updated->tags())->toHaveCount(1)
        ->and($updated->tags()[0]->id())->toBe($existing->id())
        ->and($tags->count())->toBe(1);
});

test('it creates the tag when no tag with that name exists yet', function () {
    $tasks = new InMemoryTaskRepository;
    $tags = new InMemoryTagRepository;
    $task = (new CreateTask($tasks))(new CreateTaskData('Plan the trip', null, null));

    $updated = (new AttachTag($tasks, $tags))($task->id(), 'urgent');

    expect($updated->tags())->toHaveCount(1)
        ->and($updated->tags()[0]->name())->toBe('urgent')
        ->and($tags->count())->toBe(1);
});

test('it throws TaskNotFoundException when the task does not exist', function () {
    $attachTag = new AttachTag(new InMemoryTaskRepository, new InMemoryTagRepository);

    $attachTag(999, 'urgent');
})->throws(TaskNotFoundException::class);
