<?php

declare(strict_types=1);

use App\Application\Task\DataTransferObjects\CreateTaskData;
use App\Application\Task\UseCases\AttachTag;
use App\Application\Task\UseCases\CreateTask;
use App\Application\Task\UseCases\DetachTag;
use App\Domain\Task\Exceptions\TagNotAttachedException;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use Tests\Fakes\InMemoryTagRepository;
use Tests\Fakes\InMemoryTaskRepository;

test('it detaches an attached tag', function () {
    $tasks = new InMemoryTaskRepository;
    $tags = new InMemoryTagRepository;
    $task = (new CreateTask($tasks))(new CreateTaskData('Plan the trip', null, null));
    $withTag = (new AttachTag($tasks, $tags))($task->id(), 'urgent');
    $tagId = $withTag->tags()[0]->id();

    $updated = (new DetachTag($tasks))($task->id(), $tagId);

    expect($updated->tags())->toBeEmpty();
});

test('it throws TaskNotFoundException when the task does not exist', function () {
    $detachTag = new DetachTag(new InMemoryTaskRepository);

    $detachTag(999, 1);
})->throws(TaskNotFoundException::class);

test('it throws TagNotAttachedException when the tag is not attached', function () {
    $tasks = new InMemoryTaskRepository;
    $task = (new CreateTask($tasks))(new CreateTaskData('Plan the trip', null, null));

    $detachTag = new DetachTag($tasks);

    $detachTag($task->id(), 999);
})->throws(TagNotAttachedException::class);
