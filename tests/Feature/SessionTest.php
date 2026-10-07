<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

test('it shows the login page to a guest', function () {
    $this->get('/login')->assertOk()->assertViewIs('auth.login');
});

test('it redirects an authenticated user away from the login page', function () {
    $this->actingAs(User::factory()->create())
        ->get('/login')
        ->assertRedirect();
});

test('it logs a user in with valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'demo@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->post('/login', [
        'email' => 'demo@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});

test('it rejects invalid credentials', function () {
    User::factory()->create([
        'email' => 'demo@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->from('/login')->post('/login', [
        'email' => 'demo@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect('/login')->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('it blocks access to the dashboard for guests', function () {
    $this->get('/dashboard')->assertRedirect('/login');
});

test('it allows an authenticated user to view the dashboard', function () {
    $this->actingAs(User::factory()->create())
        ->get('/dashboard')
        ->assertOk()
        ->assertViewIs('dashboard');
});

test('it logs the user out', function () {
    $this->actingAs(User::factory()->create())
        ->post('/logout')
        ->assertRedirect('/login');

    $this->assertGuest();
});
