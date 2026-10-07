<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $task_id
 * @property string $title
 * @property bool $completed
 */
final class EloquentSubtask extends Model
{
    protected $table = 'subtasks';

    /** @var list<string> */
    protected $fillable = [
        'title',
        'completed',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'completed' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<EloquentTask, $this>
     */
    public function task(): BelongsTo
    {
        return $this->belongsTo(EloquentTask::class, 'task_id');
    }
}
