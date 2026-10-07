<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * A simple, infrastructure-level activity feed, written to by queued event
 * listeners (see App\Listeners). Not a domain concept itself.
 *
 * @property int $id
 * @property string $description
 * @property Carbon $created_at
 */
final class ActivityLog extends Model
{
    protected $fillable = [
        'description',
    ];
}
