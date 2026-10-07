<?php

declare(strict_types=1);

use App\Domain\Task\Enums\TaskStatus;
use App\Infrastructure\Persistence\Eloquent\Models\EloquentTask;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates a task', function () {
    $response = $this->postJson('/api/tasks', [
        'title' => 'Write the API tests',
        'description' => 'Cover the full CRUD cycle',
        'due_date' => now()->addWeek()->format('Y-m-d'),
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.title', 'Write the API tests')
        ->assertJsonPath('data.status', 'pending')
        ->assertJsonStructure([
            'data' => ['id', 'title', 'description', 'due_date', 'status', 'created_at', 'updated_at'],
        ]);

    $this->assertDatabaseHas('tasks', [
        'title' => 'Write the API tests',
        'status' => TaskStatus::Pending->value,
    ]);
});

test('it responds with JSON even when the client sends no Accept header', function () {
    $response = $this->post('/api/tasks', []);

    $response->assertStatus(422)
        ->assertJson(fn ($json) => $json->has('message')->etc());
});

test('it responds with JSON for an unmatched api route', function () {
    $response = $this->get('/api/this-route-does-not-exist');

    $response->assertStatus(404)
        ->assertJson(fn ($json) => $json->has('message')->etc());
});

test('it rejects a task with a missing title', function () {
    $response = $this->postJson('/api/tasks', [
        'description' => 'No title provided',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors('title');

    $this->assertDatabaseCount('tasks', 0);
});

test('it rejects a task with a due date in the past via the domain rule', function () {
    $response = $this->postJson('/api/tasks', [
        'title' => 'Backdated task',
        'due_date' => now()->subDay()->format('Y-m-d'),
    ]);

    $response->assertStatus(422)
        ->assertJsonPath('message', 'Task due date must not be in the past.');

    $this->assertDatabaseCount('tasks', 0);
});

test('it lists tasks', function () {
    EloquentTask::factory()->count(3)->create();

    $response = $this->getJson('/api/tasks');

    $response->assertOk()->assertJsonCount(3, 'data');
});

test('it shows a single task', function () {
    $task = EloquentTask::factory()->create(['title' => 'Inspect me']);

    $response = $this->getJson("/api/tasks/{$task->id}");

    $response->assertOk()->assertJsonPath('data.title', 'Inspect me');
});

test('it returns 404 when showing a missing task', function () {
    $response = $this->getJson('/api/tasks/999');

    $response->assertNotFound()
        ->assertJsonPath('message', 'Task with ID [999] was not found.');
});

test('it updates a task', function () {
    $task = EloquentTask::factory()->create(['title' => 'Old title']);

    $response = $this->putJson("/api/tasks/{$task->id}", [
        'title' => 'New title',
        'description' => 'New description',
    ]);

    $response->assertOk()->assertJsonPath('data.title', 'New title');

    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'title' => 'New title']);
});

test('it returns 404 when updating a missing task', function () {
    $response = $this->putJson('/api/tasks/999', ['title' => 'Anything']);

    $response->assertNotFound();
});

test('it completes a pending task', function () {
    $task = EloquentTask::factory()->create();

    $response = $this->postJson("/api/tasks/{$task->id}/complete");

    $response->assertOk()->assertJsonPath('data.status', 'completed');

    $this->assertDatabaseHas('tasks', ['id' => $task->id, 'status' => TaskStatus::Completed->value]);
});

test('it returns 409 when completing an already completed task', function () {
    $task = EloquentTask::factory()->completed()->create();

    $response = $this->postJson("/api/tasks/{$task->id}/complete");

    $response->assertStatus(409)
        ->assertJsonPath('message', "Task with ID [{$task->id}] is already completed.");
});

test('it returns 404 when completing a missing task', function () {
    $response = $this->postJson('/api/tasks/999/complete');

    $response->assertNotFound();
});

test('it deletes a task', function () {
    $task = EloquentTask::factory()->create();

    $response = $this->deleteJson("/api/tasks/{$task->id}");

    $response->assertNoContent();

    $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
});

test('it returns 404 when deleting a missing task', function () {
    $response = $this->deleteJson('/api/tasks/999');

    $response->assertNotFound();
});
