<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(fn () => Sanctum::actingAs(User::factory()->create()));

test('a repeated request with the same idempotency key returns the original response without creating a duplicate', function () {
    $payload = ['title' => 'Write the quarterly report'];
    $key = 'a1b2c3d4-e5f6-4789-a123-0123456789ab';

    $first = $this->postJson('/api/tasks', $payload, ['Idempotency-Key' => $key]);
    $second = $this->postJson('/api/tasks', $payload, ['Idempotency-Key' => $key]);

    $first->assertCreated();
    $second->assertCreated();
    $second->assertHeader('Idempotency-Replayed', 'true');

    expect($second->json('data.id'))->toBe($first->json('data.id'));

    $this->assertDatabaseCount('tasks', 1);
});

test('two requests with different idempotency keys both create a task', function () {
    $payload = ['title' => 'Write the quarterly report'];

    $this->postJson('/api/tasks', $payload, ['Idempotency-Key' => 'key-one'])->assertCreated();
    $this->postJson('/api/tasks', $payload, ['Idempotency-Key' => 'key-two'])->assertCreated();

    $this->assertDatabaseCount('tasks', 2);
});

test('requests without an idempotency key are not deduplicated', function () {
    $payload = ['title' => 'Write the quarterly report'];

    $this->postJson('/api/tasks', $payload)->assertCreated();
    $this->postJson('/api/tasks', $payload)->assertCreated();

    $this->assertDatabaseCount('tasks', 2);
});

test('a key is scoped per user, so another user is not served a replay', function () {
    $payload = ['title' => 'Write the quarterly report'];
    $key = 'shared-key';

    $this->postJson('/api/tasks', $payload, ['Idempotency-Key' => $key])->assertCreated();

    Sanctum::actingAs(User::factory()->create());

    $this->postJson('/api/tasks', $payload, ['Idempotency-Key' => $key])->assertCreated();

    $this->assertDatabaseCount('tasks', 2);
});
