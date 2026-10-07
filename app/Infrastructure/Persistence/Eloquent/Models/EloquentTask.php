<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Models;

use App\Domain\Task\Enums\TaskStatus;
use Database\Factories\EloquentTaskFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string|null $description
 * @property Carbon|null $due_date
 * @property TaskStatus $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
final class EloquentTask extends Model
{
    /** @use HasFactory<EloquentTaskFactory> */
    use HasFactory;

    protected $table = 'tasks';

    /** @var list<string> */
    protected $fillable = [
        'title',
        'description',
        'due_date',
        'status',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'status' => TaskStatus::class,
        ];
    }

    protected static function newFactory(): EloquentTaskFactory
    {
        return EloquentTaskFactory::new();
    }

    /**
     * @return HasMany<EloquentSubtask, $this>
     */
    public function subtasks(): HasMany
    {
        return $this->hasMany(EloquentSubtask::class, 'task_id');
    }

    /**
     * @return BelongsToMany<EloquentTag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(EloquentTag::class, 'task_tag', 'task_id', 'tag_id');
    }
}
