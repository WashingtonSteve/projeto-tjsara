<?php

declare(strict_types=1);

namespace App\Application\Task\UseCases;

use App\Domain\Tag\Entities\Tag;
use App\Domain\Tag\Repositories\TagRepositoryInterface;
use App\Domain\Task\Entities\Task;
use App\Domain\Task\Exceptions\TaskNotFoundException;
use App\Domain\Task\Repositories\TaskRepositoryInterface;

final class AttachTag
{
    public function __construct(
        private readonly TaskRepositoryInterface $tasks,
        private readonly TagRepositoryInterface $tagRepository,
    ) {}

    /**
     * Finds the tag by name or creates it, then attaches it to the task.
     */
    public function __invoke(int $taskId, string $tagName): Task
    {
        $task = $this->tasks->find($taskId) ?? throw TaskNotFoundException::withId($taskId);

        $tag = $this->tagRepository->findByName($tagName) ?? $this->tagRepository->save(Tag::create($tagName));

        $task->attachTag($tag);

        return $this->tasks->save($task);
    }
}
