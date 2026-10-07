<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\Eloquent\Models\EloquentTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(fn () => Sanctum::actingAs(User::factory()->create()));

test('it adds a subtask to a task', function () {
    $task = EloquentTask::factory()->create();

    $response = $this->postJson("/api/tasks/{$task->id}/subtasks", [
        'title' => 'Book flights',
    ]);

    $response->assertCreated()
        ->assertJsonPath('data.subtasks.0.title', 'Book flights')
        ->assertJsonPath('data.subtasks.0.completed', false);
});

test('it rejects a subtask with a missing title', function () {
    $task = EloquentTask::factory()->create();

    $response = $this->postJson("/api/tasks/{$task->id}/subtasks", []);

    $response->assertUnprocessable()->assertJsonValidationErrors('title');
});

test('it returns 404 when adding a subtask to a missing task', function () {
    $response = $this->postJson('/api/tasks/999/subtasks', ['title' => 'Book flights']);

    $response->assertNotFound();
});

test('it completes a subtask', function () {
    $task = EloquentTask::factory()->create();
    $subtask = $task->subtasks()->create(['title' => 'Book flights', 'completed' => false]);

    $response = $this->postJson("/api/tasks/{$task->id}/subtasks/{$subtask->id}/complete");

    $response->assertOk()->assertJsonPath('data.subtasks.0.completed', true);
});

test('it returns 404 when completing a missing subtask', function () {
    $task = EloquentTask::factory()->create();

    $response = $this->postJson("/api/tasks/{$task->id}/subtasks/999/complete");

    $response->assertNotFound();
});

test('it removes a subtask', function () {
    $task = EloquentTask::factory()->create();
    $subtask = $task->subtasks()->create(['title' => 'Book flights', 'completed' => false]);

    $response = $this->deleteJson("/api/tasks/{$task->id}/subtasks/{$subtask->id}");

    $response->assertOk()->assertJsonCount(0, 'data.subtasks');

    $this->assertDatabaseMissing('subtasks', ['id' => $subtask->id]);
});

test('it returns 409 when completing a task that has a pending subtask', function () {
    $task = EloquentTask::factory()->create();
    $task->subtasks()->create(['title' => 'Book flights', 'completed' => false]);

    $response = $this->postJson("/api/tasks/{$task->id}/complete");

    $response->assertStatus(409)
        ->assertJsonPath('message', "Task with ID [{$task->id}] has pending subtasks and cannot be completed.");
});

test('it completes a task once every subtask is completed', function () {
    $task = EloquentTask::factory()->create();
    $task->subtasks()->create(['title' => 'Book flights', 'completed' => true]);

    $response = $this->postJson("/api/tasks/{$task->id}/complete");

    $response->assertOk()->assertJsonPath('data.status', 'completed');
});
