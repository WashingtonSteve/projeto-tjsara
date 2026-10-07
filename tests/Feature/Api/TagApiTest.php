<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\Eloquent\Models\EloquentTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(fn () => Sanctum::actingAs(User::factory()->create()));

test('it creates a tag', function () {
    $response = $this->postJson('/api/tags', ['name' => 'urgent']);

    $response->assertCreated()->assertJsonPath('data.name', 'urgent');

    $this->assertDatabaseHas('tags', ['name' => 'urgent']);
});

test('it rejects a tag with a missing name', function () {
    $response = $this->postJson('/api/tags', []);

    $response->assertUnprocessable()->assertJsonValidationErrors('name');
});

test('it lists tags', function () {
    $this->postJson('/api/tags', ['name' => 'urgent']);
    $this->postJson('/api/tags', ['name' => 'personal']);

    $response = $this->getJson('/api/tags');

    $response->assertOk()->assertJsonCount(2, 'data');
});

test('it attaches a tag to a task by name, creating it if needed', function () {
    $task = EloquentTask::factory()->create();

    $response = $this->postJson("/api/tasks/{$task->id}/tags", ['name' => 'urgent']);

    $response->assertCreated()->assertJsonPath('data.tags.0.name', 'urgent');

    $this->assertDatabaseHas('tags', ['name' => 'urgent']);
    $this->assertDatabaseCount('task_tag', 1);
});

test('it reuses an existing tag when attaching by the same name twice', function () {
    $task = EloquentTask::factory()->create();

    $this->postJson("/api/tasks/{$task->id}/tags", ['name' => 'urgent']);
    $this->postJson("/api/tasks/{$task->id}/tags", ['name' => 'urgent']);

    $this->assertDatabaseCount('tags', 1);
    $this->assertDatabaseCount('task_tag', 1);
});

test('it returns 404 when attaching a tag to a missing task', function () {
    $response = $this->postJson('/api/tasks/999/tags', ['name' => 'urgent']);

    $response->assertNotFound();
});

test('it detaches a tag from a task', function () {
    $task = EloquentTask::factory()->create();
    $attached = $this->postJson("/api/tasks/{$task->id}/tags", ['name' => 'urgent']);
    $tagId = $attached->json('data.tags.0.id');

    $response = $this->deleteJson("/api/tasks/{$task->id}/tags/{$tagId}");

    $response->assertOk()->assertJsonCount(0, 'data.tags');

    $this->assertDatabaseCount('task_tag', 0);
});

test('it returns 404 when detaching a tag that is not attached', function () {
    $task = EloquentTask::factory()->create();

    $response = $this->deleteJson("/api/tasks/{$task->id}/tags/999");

    $response->assertNotFound();
});
