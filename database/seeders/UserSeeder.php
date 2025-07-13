<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $manager = User::where('email', 'manager@example.com')->first();
        if (! $manager) {
            $manager = User::create([
                'name' => 'Manager',
                'email' => 'manager@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $user = User::where('email', 'user@example.com')->first();
        if (! $user) {
            $user = User::create([
                'name' => 'User',
                'email' => 'user@example.com',
                'password' => bcrypt('password'),
            ]);
        }

        $user2 = User::where('email', 'user2@example.com')->first();
        if (! $user2) {
            $user2 = User::create([
                'name' => 'User2',
                'email' => 'user2@example.com',
                'password' => bcrypt('password'),
            ]);
        }
    }
}
