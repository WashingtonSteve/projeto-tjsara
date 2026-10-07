<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('it issues a token for valid credentials', function () {
    User::factory()->create([
        'email' => 'demo@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->postJson('/api/auth/token', [
        'email' => 'demo@example.com',
        'password' => 'password',
    ]);

    $response->assertCreated()->assertJsonStructure(['token']);
});

test('it rejects an unknown email', function () {
    $response = $this->postJson('/api/auth/token', [
        'email' => 'nobody@example.com',
        'password' => 'password',
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors('email');
});

test('it rejects an incorrect password', function () {
    User::factory()->create([
        'email' => 'demo@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->postJson('/api/auth/token', [
        'email' => 'demo@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertUnprocessable()->assertJsonValidationErrors('email');
});

test('protected routes reject a request with no token', function () {
    $response = $this->getJson('/api/tasks');

    $response->assertUnauthorized();
});

test('protected routes accept a request with a valid token', function () {
    $user = User::factory()->create([
        'email' => 'demo@example.com',
        'password' => Hash::make('password'),
    ]);

    $token = $this->postJson('/api/auth/token', [
        'email' => 'demo@example.com',
        'password' => 'password',
    ])->json('token');

    $response = $this->getJson('/api/tasks', ['Authorization' => "Bearer {$token}"]);

    $response->assertOk();
    expect($user->tokens()->count())->toBe(1);
});

test('logout revokes the current token', function () {
    User::factory()->create([
        'email' => 'demo@example.com',
        'password' => Hash::make('password'),
    ]);

    $token = $this->postJson('/api/auth/token', [
        'email' => 'demo@example.com',
        'password' => 'password',
    ])->json('token');

    $this->deleteJson('/api/auth/token', [], ['Authorization' => "Bearer {$token}"])
        ->assertNoContent();

    // Sanctum's guard memoizes the resolved user for the lifetime of this
    // test's container, so without this, the next call below would still
    // see the already-resolved user instead of re-validating the token.
    Auth::forgetGuards();

    $this->getJson('/api/tasks', ['Authorization' => "Bearer {$token}"])
        ->assertUnauthorized();
});
