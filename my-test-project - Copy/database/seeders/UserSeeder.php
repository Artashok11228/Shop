<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $password = Hash::make('password');

        for ($number = 1; $number <= 10; $number++) {
            User::firstOrCreate(
                ['email' => "customer{$number}@example.com"],
                [
                    'name' => fake()->name(),
                    'email_verified_at' => now(),
                    'password' => $password,
                ]
            );
        }
    }
}
