<?php

declare(strict_types=1);

use App\Application\Tag\UseCases\CreateTag;
use App\Application\Tag\UseCases\ListTags;
use Tests\Fakes\InMemoryTagRepository;

test('it returns an empty list when there are no tags', function () {
    $listTags = new ListTags(new InMemoryTagRepository);

    expect($listTags())->toBe([]);
});

test('it returns every persisted tag', function () {
    $repository = new InMemoryTagRepository;
    $createTag = new CreateTag($repository);
    $listTags = new ListTags($repository);

    $createTag('urgent');
    $createTag('personal');

    expect($listTags())->toHaveCount(2);
});
