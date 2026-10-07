<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Task\Enums\TaskStatus;
use App\Infrastructure\Persistence\Eloquent\Models\EloquentTask;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<EloquentTask>
 */
final class EloquentTaskFactory extends Factory
{
    protected $model = EloquentTask::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->optional(0.7)->paragraph(),
            'due_date' => fake()->optional(0.7)->dateTimeBetween('now', '+1 month')?->format('Y-m-d'),
            'status' => TaskStatus::Pending,
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TaskStatus::Completed,
        ]);
    }

    public function withoutDueDate(): static
    {
        return $this->state(fn (array $attributes) => [
            'due_date' => null,
        ]);
    }
}
