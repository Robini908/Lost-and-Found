<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'reward_points' => 1000,
            ]
        )->assignRole('admin');

        // Create moderator user
        User::firstOrCreate(
            ['email' => 'moderator@example.com'],
            [
                'name' => 'Moderator User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'reward_points' => 500,
            ]
        )->assignRole('moderator');

        // Create regular users
        for ($i = 1; $i <= 5; $i++) {
            User::firstOrCreate(
                ['email' => "user{$i}@example.com"],
                [
                    'name' => "Test User {$i}",
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'reward_points' => rand(0, 200),
                ]
            )->assignRole('user');
        }
    }
}
