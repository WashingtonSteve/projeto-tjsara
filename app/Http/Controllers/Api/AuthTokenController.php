<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * Issues and revokes Sanctum personal access tokens for API clients that
 * cannot hold a session (curl, Postman, server-to-server). The Blade
 * dashboard authenticates differently, via Sanctum's stateful SPA session
 * (see SessionController) - both flows end up authorized by the same
 * auth:sanctum guard on /api routes.
 */
final class AuthTokenController extends Controller
{
    public function store(LoginRequest $request): JsonResponse
    {
        $user = User::query()->where('email', $request->string('email'))->first();

        if (! $user || ! Hash::check($request->string('password')->toString(), $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'token' => $user->createToken('api')->plainTextToken,
        ], Response::HTTP_CREATED);
    }

    public function destroy(Request $request): Response
    {
        $token = $request->user()?->currentAccessToken();

        if ($token instanceof PersonalAccessToken) {
            $token->delete();
        }

        return response()->noContent();
    }
}
