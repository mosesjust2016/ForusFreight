<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@forusfl.co.zm',
            'password' => \Illuminate\Support\Facades\Hash::make('Forus@2026!'),
            'email_verified_at' => now(),
            'phone_verified_at' => now(),
            'is_admin' => true,
        ]);
    }
}
