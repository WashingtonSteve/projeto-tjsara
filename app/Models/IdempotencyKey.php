<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Stores the replayed response for a client-supplied Idempotency-Key, so a
 * retried POST returns the original result instead of creating a duplicate
 * resource. This is HTTP-layer infrastructure, not a Task domain concept -
 * it deliberately lives outside app/Domain and app/Infrastructure/Persistence.
 *
 * @property int $id
 * @property int $user_id
 * @property string $key
 * @property int $response_status
 * @property string $response_body
 */
final class IdempotencyKey extends Model
{
    protected $fillable = [
        'user_id',
        'key',
        'response_status',
        'response_body',
    ];
}
