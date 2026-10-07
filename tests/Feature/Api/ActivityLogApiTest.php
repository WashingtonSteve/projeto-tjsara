<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\Eloquent\Models\EloquentTask;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(fn () => Sanctum::actingAs(User::factory()->create()));

test('it lists recent activity, most recent first', function () {
    $taskA = EloquentTask::factory()->create(['title' => 'Task A']);
    $taskB = EloquentTask::factory()->create(['title' => 'Task B']);

    $this->postJson("/api/tasks/{$taskA->id}/complete")->assertOk();
    $this->postJson("/api/tasks/{$taskB->id}/complete")->assertOk();

    $response = $this->getJson('/api/activity');

    $response->assertOk()->assertJsonCount(2, 'data');
    expect($response->json('data.0.description'))->toContain('Task B');
});

test('it requires authentication', function () {
    Auth::forgetGuards();

    $this->withHeaders(['Authorization' => 'Bearer invalid'])
        ->getJson('/api/activity')
        ->assertUnauthorized();
});
