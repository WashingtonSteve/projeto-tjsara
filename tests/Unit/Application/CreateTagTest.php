<?php

declare(strict_types=1);

use App\Application\Tag\UseCases\CreateTag;
use App\Domain\Tag\Exceptions\InvalidTagDataException;
use Tests\Fakes\InMemoryTagRepository;

test('it creates and persists a new tag', function () {
    $repository = new InMemoryTagRepository;

    $tag = (new CreateTag($repository))('urgent');

    expect($tag->id())->not->toBeNull()
        ->and($tag->name())->toBe('urgent')
        ->and($repository->count())->toBe(1);
});

test('it propagates domain validation failures', function () {
    $createTag = new CreateTag(new InMemoryTagRepository);

    $createTag('');
})->throws(InvalidTagDataException::class);
