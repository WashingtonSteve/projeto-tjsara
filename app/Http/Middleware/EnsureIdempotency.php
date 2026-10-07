<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\IdempotencyKey;
use Closure;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Lets a client safely retry a POST (e.g. after a dropped connection)
 * without creating a duplicate resource. Opt-in: a request with no
 * Idempotency-Key header behaves exactly as before.
 */
final class EnsureIdempotency
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('Idempotency-Key');
        $userId = $request->user()?->id;

        if (! $key || ! $userId) {
            return $next($request);
        }

        $existing = IdempotencyKey::query()
            ->where('user_id', $userId)
            ->where('key', $key)
            ->first();

        if ($existing) {
            return response($existing->response_body, $existing->response_status)
                ->header('Content-Type', 'application/json')
                ->header('Idempotency-Replayed', 'true');
        }

        $response = $next($request);

        if ($response->getStatusCode() < 500) {
            try {
                IdempotencyKey::query()->create([
                    'user_id' => $userId,
                    'key' => $key,
                    'response_status' => $response->getStatusCode(),
                    'response_body' => $response->getContent(),
                ]);
            } catch (QueryException) {
                // Lost a race against a concurrent identical request. The
                // other request already recorded this key; this response
                // is still valid for the caller, it just won't be replayed.
            }
        }

        return $response;
    }
}
