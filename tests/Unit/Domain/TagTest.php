<?php

declare(strict_types=1);

use App\Domain\Tag\Entities\Tag;
use App\Domain\Tag\Exceptions\InvalidTagDataException;

test('create builds a tag with a trimmed name', function () {
    $tag = Tag::create('  urgent  ');

    expect($tag->id())->toBeNull()
        ->and($tag->name())->toBe('urgent');
});

test('create rejects an empty name', function (string $name) {
    Tag::create($name);
})->with(['', '   '])->throws(InvalidTagDataException::class, 'Tag name must not be empty.');

test('create rejects a name longer than 50 characters', function () {
    Tag::create(str_repeat('a', 51));
})->throws(InvalidTagDataException::class, 'Tag name must not exceed 50 characters.');

test('reconstitute rebuilds a tag from trusted persisted state', function () {
    $tag = Tag::reconstitute(id: 3, name: 'urgent');

    expect($tag->id())->toBe(3)
        ->and($tag->name())->toBe('urgent');
});
