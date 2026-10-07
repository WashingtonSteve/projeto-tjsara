<?php

declare(strict_types=1);

use App\Infrastructure\Persistence\Eloquent\Models\EloquentTask;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;

uses(RefreshDatabase::class);

beforeEach(fn () => Sanctum::actingAs(User::factory()->create()));

test('completing a task through the API logs an activity entry', function () {
    $task = EloquentTask::factory()->create(['title' => 'Ship the release']);

    $this->postJson("/api/tasks/{$task->id}/complete")->assertOk();

    $this->assertDatabaseHas('activity_logs', [
        'description' => "Task \"Ship the release\" (#{$task->id}) was completed.",
    ]);
});

test('it does not log activity for actions other than completing a task', function () {
    EloquentTask::factory()->create();

    expect(ActivityLog::query()->count())->toBe(0);
});
