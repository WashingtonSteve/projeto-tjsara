<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Creates a demo account so a reviewer can log in and poke around
     * without creating their own user. Not a credential meant to protect
     * anything real - this project ships with no production data.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
