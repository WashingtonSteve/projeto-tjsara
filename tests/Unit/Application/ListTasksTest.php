<?php

declare(strict_types=1);

use App\Application\Task\DataTransferObjects\CreateTaskData;
use App\Application\Task\UseCases\CreateTask;
use App\Application\Task\UseCases\ListTasks;
use Tests\Fakes\InMemoryTaskRepository;

test('it returns an empty list when there are no tasks', function () {
    $listTasks = new ListTasks(new InMemoryTaskRepository);

    expect($listTasks())->toBe([]);
});

test('it returns every persisted task', function () {
    $repository = new InMemoryTaskRepository;
    $createTask = new CreateTask($repository);
    $listTasks = new ListTasks($repository);

    $createTask(new CreateTaskData('First task', null, null));
    $createTask(new CreateTaskData('Second task', null, null));

    $tasks = $listTasks();

    expect($tasks)->toHaveCount(2)
        ->and($tasks[0]->title())->toBe('First task')
        ->and($tasks[1]->title())->toBe('Second task');
});
