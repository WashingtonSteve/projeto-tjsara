<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Task\Repositories\TaskRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\Repositories\EloquentTaskRepository;
use Illuminate\Support\ServiceProvider;

final class DomainServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TaskRepositoryInterface::class, EloquentTaskRepository::class);
    }
}
