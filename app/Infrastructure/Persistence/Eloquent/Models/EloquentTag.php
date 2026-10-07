<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Eloquent\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 */
final class EloquentTag extends Model
{
    protected $table = 'tags';

    /** @var list<string> */
    protected $fillable = [
        'name',
    ];

    /**
     * @return BelongsToMany<EloquentTask, $this>
     */
    public function tasks(): BelongsToMany
    {
        return $this->belongsToMany(EloquentTask::class, 'task_tag', 'tag_id', 'task_id');
    }
}
